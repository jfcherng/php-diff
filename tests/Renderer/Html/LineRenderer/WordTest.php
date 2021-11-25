<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Test\Renderer\Html\LineRenderer;

use Jfcherng\Diff\Renderer\Html\LineRenderer\Word;
use Jfcherng\Diff\Renderer\RendererConstant;
use Jfcherng\Utility\MbString;
use PHPUnit\Framework\TestCase;

/**
 * Test general methods from the Word.
 *
 * @coversNothing
 *
 * @internal
 */
final class WordTest extends TestCase
{
    /**
     * Test the Word::render can work with "wordGlues".
     *
     * @covers \Jfcherng\Diff\Renderer\Html\LineRenderer\Word::render
     */
    public function testRenderWordGlues1(): void
    {
        $word = new Word([], ['wordGlues' => [' ', '-']]);
        $mbOld = new MbString('good-looking-x');
        $mbNew = new MbString('good--y');

        $word->render($mbOld, $mbNew);

        $oldDiff = str_replace(RendererConstant::HTML_CLOSURES, RendererConstant::HTML_CLOSURES_DEL, $mbOld->get());
        $newDiff = str_replace(RendererConstant::HTML_CLOSURES, RendererConstant::HTML_CLOSURES_INS, $mbNew->get());

        static::assertSame('good-<del>looking-x</del>', $oldDiff);
        static::assertSame('good-<ins>-y</ins>', $newDiff);
    }

    /**
     * Test the Word::render can work with "wordGlues".
     *
     * @covers \Jfcherng\Diff\Renderer\Html\LineRenderer\Word::render
     *
     * @see https://github.com/jfcherng/php-diff/pull/25
     */
    public function testRenderWordGlues2(): void
    {
        $word = new Word([], ['wordGlues' => [' ', '-']]);
        $mbOld = new MbString('of the Indo-Euopearn legguanas, Porto-Idno-Eorpuaen, did not have aieltrcs. Msot of the lanaguges in this flamiy do not hvae diiefnte or ieiinfntde atrciels: three is no actirle in Ltain or Ssknarit, nor in some meodrn Indo-Eoeapurn lgagaenus, scuh as the falmeiis of Salvic lnggaeuas (epexct for Bulraiagn and Mneiodaacn, whcih are reahtr dviiittcsne amnog the Saivlc lagauegns in thier gmrmaar), Btialc leaauggns and mnay Indo-Aaryn lgugneaas. Aluogthh Csiacslal Greek had a dtiniefe artlcie (wichh has srivvued itno Modren Gerek and wichh bears snotrg fcaiutonnl raeelmnscbe to the Gramen deiinfte arltcie, whcih it is rtealed to), the erailer Hmeoirc Geerk used this alctrie llrgeay as a prnuoon or dioravsttmnee, wareehs the erlaseit known form of Geerk konwn as Meayenacn Gerek did not have any acrlties. Atilcres dvlpeeoed inleenddpenty in svaeerl lagnauge fmieials. In mnay lagnuegas, the form of the atrlice may vary acdrocnig to the gneedr, nbmuer, or csae of its nuon. In smoe laganegus the artcile may be the only idiaitnocn of the case. Many languages do not utilize articles at all, and may use other ways of denoting old versus incipient information, such as topic comment constructions.');
        $mbNew = new MbString('of the Indo-European languages, Proto-Indo-European, did not have articles. Most of the languages in this family do not have definite or indefinite articles: there is no article in Latin or Sanskrit, nor in some modern Indo-European languages, such as the families of Slavic languages (epexct for Bulraiagn and Mneiodaacn, whcih are reahtr dviiittcsne amnog the Saivlc lagauegns in thier gmrmaar), Btialc leaauggns and mnay Indo-Aaryn lgugneaas. Aluogthh Csiacslal Greek had a dtiniefe artlcie (wichh has srivvued itno Modren Gerek and wichh bears snotrg fcaiutonnl raeelmnscbe to the Gramen deiinfte arltcie, whcih it is rtealed to), the erailer Hmeoirc Geerk used this alctrie llrgeay as a prnuoon or dioravsttmnee, wareehs the erlaseit known form of Geerk konwn as Meayenacn Gerek did not have any acrlties. Atilcres dvlpeeoed inleenddpenty in svaeerl lagnauge fmieials. In mnay lagnuegas, the form of the atrlice may vary acdrocnig to the gneedr, nbmuer, or csae of its nuon. In smoe laganegus the artcile may be the only idiaitnocn of the case. Many languages do not utilize articles at all, and may use other ways of denoting old versus incipient information, such as topic comment constructions.');

        $word->render($mbOld, $mbNew);

        $oldDiff = str_replace(RendererConstant::HTML_CLOSURES, RendererConstant::HTML_CLOSURES_DEL, $mbOld->get());
        $newDiff = str_replace(RendererConstant::HTML_CLOSURES, RendererConstant::HTML_CLOSURES_INS, $mbNew->get());

        static::assertSame('of the Indo-<del>Euopearn legguanas</del>, <del>Porto-Idno-Eorpuaen</del>, did not have <del>aieltrcs</del>. <del>Msot</del> of the <del>lanaguges</del> in this <del>flamiy</del> do not <del>hvae diiefnte</del> or <del>ieiinfntde atrciels</del>: <del>three</del> is no <del>actirle</del> in <del>Ltain</del> or <del>Ssknarit</del>, nor in some <del>meodrn</del> Indo-<del>Eoeapurn lgagaenus</del>, <del>scuh</del> as the <del>falmeiis</del> of <del>Salvic lnggaeuas</del> (epexct for Bulraiagn and Mneiodaacn, whcih are reahtr dviiittcsne amnog the Saivlc lagauegns in thier gmrmaar), Btialc leaauggns and mnay Indo-Aaryn lgugneaas. Aluogthh Csiacslal Greek had a dtiniefe artlcie (wichh has srivvued itno Modren Gerek and wichh bears snotrg fcaiutonnl raeelmnscbe to the Gramen deiinfte arltcie, whcih it is rtealed to), the erailer Hmeoirc Geerk used this alctrie llrgeay as a prnuoon or dioravsttmnee, wareehs the erlaseit known form of Geerk konwn as Meayenacn Gerek did not have any acrlties. Atilcres dvlpeeoed inleenddpenty in svaeerl lagnauge fmieials. In mnay lagnuegas, the form of the atrlice may vary acdrocnig to the gneedr, nbmuer, or csae of its nuon. In smoe laganegus the artcile may be the only idiaitnocn of the case. Many languages do not utilize articles at all, and may use other ways of denoting old versus incipient information, such as topic comment constructions.', $oldDiff);
        static::assertSame('of the Indo-<ins>European languages</ins>, <ins>Proto-Indo-European</ins>, did not have <ins>articles</ins>. <ins>Most</ins> of the <ins>languages</ins> in this <ins>family</ins> do not <ins>have definite</ins> or <ins>indefinite articles</ins>: <ins>there</ins> is no <ins>article</ins> in <ins>Latin</ins> or <ins>Sanskrit</ins>, nor in some <ins>modern</ins> Indo-<ins>European languages</ins>, <ins>such</ins> as the <ins>families</ins> of <ins>Slavic languages</ins> (epexct for Bulraiagn and Mneiodaacn, whcih are reahtr dviiittcsne amnog the Saivlc lagauegns in thier gmrmaar), Btialc leaauggns and mnay Indo-Aaryn lgugneaas. Aluogthh Csiacslal Greek had a dtiniefe artlcie (wichh has srivvued itno Modren Gerek and wichh bears snotrg fcaiutonnl raeelmnscbe to the Gramen deiinfte arltcie, whcih it is rtealed to), the erailer Hmeoirc Geerk used this alctrie llrgeay as a prnuoon or dioravsttmnee, wareehs the erlaseit known form of Geerk konwn as Meayenacn Gerek did not have any acrlties. Atilcres dvlpeeoed inleenddpenty in svaeerl lagnauge fmieials. In mnay lagnuegas, the form of the atrlice may vary acdrocnig to the gneedr, nbmuer, or csae of its nuon. In smoe laganegus the artcile may be the only idiaitnocn of the case. Many languages do not utilize articles at all, and may use other ways of denoting old versus incipient information, such as topic comment constructions.', $newDiff);
    }
}
