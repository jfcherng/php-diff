<?php declare(strict_types = 1);

namespace PHPStan\Command;

use Clue\React\NDJson\Decoder;
use Clue\React\NDJson\Encoder;
use PHPStan\Analyser\FileAnalyser;
use PHPStan\Analyser\NodeScopeResolver;
use PHPStan\DependencyInjection\Container;
use PHPStan\Rules\Registry;
use React\EventLoop\StreamSelectLoop;
use React\Socket\ConnectionInterface;
use React\Socket\TcpConnector;
use React\Stream\ReadableStreamInterface;
use React\Stream\WritableStreamInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class WorkerCommand extends Command
{

	private const NAME = 'worker';

	/** @var string[] */
	private array $composerAutoloaderProjectPaths;

	/**
	 * @param string[] $composerAutoloaderProjectPaths
	 */
	public function __construct(
		array $composerAutoloaderProjectPaths
	)
	{
		parent::__construct();
		$this->composerAutoloaderProjectPaths = $composerAutoloaderProjectPaths;
	}

	protected function configure(): void
	{
		$this->setName(self::NAME)
			->setDescription('(Internal) Support for parallel analysis.')
			->setDefinition([
				new InputArgument('paths', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Paths with source code to run analysis on'),
				new InputOption('paths-file', null, InputOption::VALUE_REQUIRED, 'Path to a file with a list of paths to run analysis on'),
				new InputOption('configuration', 'c', InputOption::VALUE_REQUIRED, 'Path to project configuration file'),
				new InputOption(AnalyseCommand::OPTION_LEVEL, 'l', InputOption::VALUE_REQUIRED, 'Level of rule options - the higher the stricter'),
				new InputOption('autoload-file', 'a', InputOption::VALUE_REQUIRED, 'Project\'s additional autoload file path'),
				new InputOption('memory-limit', null, InputOption::VALUE_REQUIRED, 'Memory limit for analysis'),
				new InputOption('xdebug', null, InputOption::VALUE_NONE, 'Allow running with XDebug for debugging purposes'),
				new InputOption('port', null, InputOption::VALUE_REQUIRED),
				new InputOption('identifier', null, InputOption::VALUE_REQUIRED),
			]);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$paths = $input->getArgument('paths');
		$memoryLimit = $input->getOption('memory-limit');
		$autoloadFile = $input->getOption('autoload-file');
		$configuration = $input->getOption('configuration');
		$level = $input->getOption(AnalyseCommand::OPTION_LEVEL);
		$pathsFile = $input->getOption('paths-file');
		$allowXdebug = $input->getOption('xdebug');
		$port = $input->getOption('port');
		$identifier = $input->getOption('identifier');

		if (
			!is_array($paths)
			|| (!is_string($memoryLimit) && $memoryLimit !== null)
			|| (!is_string($autoloadFile) && $autoloadFile !== null)
			|| (!is_string($configuration) && $configuration !== null)
			|| (!is_string($level) && $level !== null)
			|| (!is_string($pathsFile) && $pathsFile !== null)
			|| (!is_bool($allowXdebug))
			|| !is_string($port)
			|| !is_string($identifier)
		) {
			throw new \PHPStan\ShouldNotHappenException();
		}

		try {
			$inceptionResult = CommandHelper::begin(
				$input,
				$output,
				$paths,
				$pathsFile,
				$memoryLimit,
				$autoloadFile,
				$this->composerAutoloaderProjectPaths,
				$configuration,
				null,
				$level,
				$allowXdebug,
				false
			);
		} catch (\PHPStan\Command\InceptionNotSuccessfulException $e) {
			return 1;
		}
		$loop = new StreamSelectLoop();

		$container = $inceptionResult->getContainer();

		$analysedFiles = $inceptionResult->getFiles();

		/** @var NodeScopeResolver $nodeScopeResolver */
		$nodeScopeResolver = $container->getByType(NodeScopeResolver::class);
		$nodeScopeResolver->setAnalysedFiles($analysedFiles);

		$analysedFiles = array_fill_keys($analysedFiles, true);

		$tcpConector = new TcpConnector($loop);
		$tcpConector->connect(sprintf('127.0.0.1:%d', $port))->done(function (ConnectionInterface $connection) use ($container, $identifier, $analysedFiles): void {
			$out = new Encoder($connection);
			$in = new Decoder($connection, true, 512, 0, $container->getParameter('parallel')['buffer']);
			$out->write(['action' => 'hello', 'identifier' => $identifier]);
			$this->runWorker($container, $out, $in, $analysedFiles);
		});

		$loop->run();

		return 0;
	}

	/**
	 * @param Container $container
	 * @param WritableStreamInterface $out
	 * @param ReadableStreamInterface $in
	 * @param array<string, true> $analysedFiles
	 */
	private function runWorker(
		Container $container,
		WritableStreamInterface $out,
		ReadableStreamInterface $in,
		array $analysedFiles
	): void
	{
		$handleError = static function (\Throwable $error) use ($out): void {
			$out->write([
				'action' => 'result',
				'result' => [
					'errors' => [$error->getMessage()],
					'dependencies' => [],
					'filesCount' => 0,
					'internalErrorsCount' => 1,
				],
			]);
			$out->end();
		};
		$out->on('error', $handleError);

		/** @var FileAnalyser $fileAnalyser */
		$fileAnalyser = $container->getByType(FileAnalyser::class);

		/** @var Registry $registry */
		$registry = $container->getByType(Registry::class);

		// collectErrors (from Analyser)
		$in->on('data', static function (array $json) use ($fileAnalyser, $registry, $out, $analysedFiles): void {
			$action = $json['action'];
			if ($action !== 'analyse') {
				return;
			}

			$internalErrorsCount = 0;
			$files = $json['files'];
			$errors = [];
			$dependencies = [];
			foreach ($files as $file) {
				try {
					$fileAnalyserResult = $fileAnalyser->analyseFile($file, $analysedFiles, $registry, null);
					$fileErrors = $fileAnalyserResult->getErrors();
					$dependencies[$file] = $fileAnalyserResult->getDependencies();
					foreach ($fileErrors as $fileError) {
						$errors[] = $fileError;
					}
				} catch (\Throwable $t) {
					$internalErrorsCount++;
					$internalErrorMessage = sprintf('Internal error: %s in file %s', $t->getMessage(), $file);
					$internalErrorMessage .= sprintf(
						'%sRun PHPStan with --debug option and post the stack trace to:%s%s',
						"\n",
						"\n",
						'https://github.com/phpstan/phpstan/issues/new'
					);
					$errors[] = $internalErrorMessage;
				}
			}

			$out->write([
				'action' => 'result',
				'result' => [
					'errors' => $errors,
					'dependencies' => $dependencies,
					'filesCount' => count($files),
					'internalErrorsCount' => $internalErrorsCount,
				]]);
		});
		$in->on('error', $handleError);
	}

	public function haha(int $i): void
	{
		function () use ($i) {

		};
	}

	/**
	 * @param int $b
	 */
	public function doLorem(int $a): void
	{
		echo 'haha';
		return;
	}

}
