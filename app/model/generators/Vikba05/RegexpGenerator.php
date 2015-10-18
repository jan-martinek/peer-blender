<?php

namespace Model\Generator\Vikba05;

use Nette;
use Model\Generator\IGenerator;
use Model\Generator\SimpleQuestionset;

class RegexpGenerator extends Nette\Object implements IGenerator
{
    
    
    public function getPreface() 
    {
        return 'U této lekce je podstatné pracovat správně se soubory: sledujte zadání, kde je napsáno, jaké formáty (HTML, Markdown, Word či jeho alternativy) máte užít a jak s nimi pracovat. Některé úkoly vyžadují, abyste soubor uložili vícekrát a přiložili průběžné verze v ZIPu — dejte si na to pozor.'
            . "\n\n"
            . "Pokud něčemu nerozumíte a nemůžete to dohledat pomocí Google, zkuste se obrátit na kolegy v diskusním fóru v ISu.";
    }

    public function getQuestions() 
    {
        
        $remember = new SimpleQuestionset('remember',
            array(
                'K čemu se používají regulární výrazy?',
                'Co jsou to zástupné znaky v aplikacích MS Word a OO Writer?',
                'Popište běžné klávesové zkratky pro funkce *Najít* a *Najít a nahradit* v programech ve vašem operačním systému (uveďte, jaký operační systém používáte).',
                'Kde najdete funkci *Najít a nahradit* v MS Word?',
                'Jaké klávesové zkratky pro hledání a nahrazování můžete využít v programu *Adobe Brackets*?',
                'Co označuje v rámci řetězce v poli *Nahradit* výraz `\1`? V jakém programu je možné tento výraz využít?',
                'Co označuje v rámci řetězce v poli *Nahradit* výraz `$1`? V jakém programu je možné tento výraz využít?'
            )
        );
        
        $apply = new SimpleQuestionset('apply',
            array(
                'Zadejte několik příkladů frází, které můžete zadat do Googlu, abyste se dozvěděli více o regulárních výrazech a jejich použití. Jeden z odkazů si vyberte a krátce shrňte obsah nalezeného článku a jeho přínos pro vás.',
                'Kdo poprvé představil koncept regulárních výrazů? Stručně popište, o koho šlo.'
            )
        );

        $regexpExamples = array(
            '^p', '?', '^t', '[-]', '^?', '<', '^#', '>', '^$', '()', '^^', '[!]', '^%', '{;}', '^v', '@', '^n', '*', '^+', '^=', '^e', '^d', '^f', '^g', '^l', '^m', '^~', '^s', '^-', '^b', '^w', '^a');
        $examplesWord = new SimpleQuestionset('apply');
        $examplesWord->addRandomizedQuestion('Jaký význam má mezi zástupnými znaky v MS Word/OO Writer `%regexp%`? Popište *vlastními slovy* a napište konkrétní příklad užití (co byste díky tomuto zástupnému znaku mohli najít).', array('regexp' => $regexpExamples), 3);  
 
        
        $regexpExamples = array(
            '[0-9]{3} [0-9]{3} [0-9]{3}',
            'čp\. {0-9}+',
            '[0-9]{3} [0-9]{2}',
            '[a-z]+@[a-z]+\.[a-z]+',
            '(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?',
            '<[a-z0-9]+>.+</[a-z0-9]+>',
            '\*\*[^*]+\*\*',
            '\[.+\]\(.+\)',
            '\n#+ .+',
            '.+',
            '...',
            '[^a]+'
        );
        $examplesPerl = new SimpleQuestionset('apply');
        $examplesPerl->addRandomizedQuestion('Popište, jaký řetězec vyhovuje regulárnímu výrazu `%regexp%`. Napište konkrétní příklad. Jaký by mohl být význam takového řetězce? Vytvořte podobný výraz pomocí zástupných znaků ve MS Wordu (či OO Writeru).', array('regexp' => $regexpExamples), 3);
        

        $ulozTo = new SimpleQuestionset('create',
            array(
                'Najděte na serveru [Ulož.to](http://uloz.to) nějaký pro vás zajímavý dokument uložený ve formátu MS Word (koncovka .doc nebo .docx), který má zároveň alespoň 3000 slov.'
                . "\n\n"
                . 'Tento dokument upravte v MS Wordu/OO Writeru tak, aby vyhovoval typografickým pravidlům (nejen z hlediska formátování, ale především z hlediska samotného textu — tzn. správné odstavce, sémanticky značené titulky, pevné mezery, správně umístěná interpunkce, české uvozovky, pomlčky a spojovníky atp.). **Původní verzi i upravený dokument** uložte a v archivu ZIP nahrejte do přílohy (společně se soubory z následující úlohy).'
                . "\n\n"
                . 'Do vstupního pole popište hledané výrazy (využívající zástupné znaky), které jste použili pro dotažení dokumentu do typograficky slušné podoby. Zároveň popište, **proč je právě pro vás tento dokument zajímavý** a jak jste jej na serveru Ulož.to našli.'
            )
        );
            
        $htmlToMarkdown = new SimpleQuestionset('create');
        $htmlToMarkdown->addRandomizedQuestion('Stáhněte zdrojový kód stránky *„%wikipage%“* na české Wikipedii. Vyřízněte z něj obsah elementu `body` a ten **pomocí funkce Najít a nahradit** v editoru *Adobe Brackets* (či ekvivalentním textovém editoru) převeďte do co nejčistší syntaxe Markdown.'
            . "\n\n"
            . 'Popište, **jaké regulární výrazy jste použili pro hledání a nahrazování** (alespoň čtyři). Během práce soubor **alespoň třikrát uložte** a odevzdejte tyto průběžné verze i výsledek v archivu ZIP (společně se soubory z předchozí úlohy).',
            array(
                'wikipage' => array(     
                    'Knihovnictví',
                    'Informační věda',
                    'Informační systém',
                    'Legislativa',
                    'Jackson Pollock',
                    'Surrealismus',
                    'Salvador Dalí',
                    'Medaile Za hrdinství',
                    'Česko',
                    'Slovensko',
                    'Rusíni',
                    'Brooklynský most',
                    'Myanmar',
                    'Bangladéš',
                    'Fair trade',
                    'Sedmá generace',
                    'Panda velká',
                    'Medvědovití',
                    'Čínská lidová republika',
                    'Filozofická fakulta Masarykovy univerzity',
                    'Filosofie',
                    'Věda'
                )
            )
        );
        
        $questions = array_merge(
            $remember->getQuestions(2),
            $apply->getQuestions(1),
            $examplesWord->getQuestions(2),
            $examplesPerl->getQuestions(2),
            $ulozTo->getQuestions(1),
            $htmlToMarkdown->getQuestions(1)
        );
        
        return $questions;
    }
    
    public function getRubrics() 
    {
        return array(
            'Jaké chyby udělal/a autor/ka v odpovědích na vědomostní otázky (1-7)? Jaké zdroje mohou poradit, jak pracovat s regulárními výrazy lépe?',
            'Jsou všechny soubory ze závěrečných dvou úkolů správně odevzdané?',
            'Je motivace pro výběr dokumentu v předposledním úkolu přesvědčivá?'
        );
    }

}
