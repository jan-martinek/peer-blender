<?php

namespace Model\Generator\Vikba05;

use Nette;
use Model\Generator\IGenerator;
use Model\Generator\SimpleQuestionset;

class TablesGenerator extends Nette\Object implements IGenerator
{
    
    
    public function getPreface() 
    {
        return 'Dejte si pozor na správné užívání pojmů.'
            . "\n\n"
            . 'Všechny praktické úkoly uložte *do jednoho sešitu, každý úkol na jeden list*. Sešit uložte jako `ukoly.xls` a odevzdejte.';
    }

    public function getQuestions() 
    {
        $termsDict = array(
            'buňka (cell)',
            'řádek (row)',
            'sloupec (column)',
            'záhlaví (hlavička)',
            'záznam (record)',
            'seznam (list)',
            'list (sheet)',
            'sešit (workbook)'
        );
        
        $terms = new SimpleQuestionset('remember');
        $terms->addRandomizedQuestion('Popište pojem *%term%* v kontextu práce s tabulkovými daty.', array('term' => $termsDict), 3);  
        
        $understand = new SimpleQuestionset('understand', array(
            'Definujte *vlastními slovy* co nejpřesněji pojem *objekt* a vysvětlete, jak se k němu vztahují pojmy *entita* a *atribut*.',
            'Definujte *vlastními slovy* co nejpřesněji pojem *entita* a vysvětlete, jak se k němu vztahují pojmy *objekt* a *atribut*.',
            'Definujte *vlastními slovy* co nejpřesněji pojem *atribut* a vysvětlete, jak se k němu vztahují pojmy *objekt* a *atribut*.'
        ));
        
        $analyze = new SimpleQuestionset('analyze',
            array(
                'Jaké znáte tabulkové procesory?',
                'Najděte na internetu oficiální stránku uživatelské podpory tabulkového procesoru, který používáte. Název procesoru a odkaz vložte do odpovědi.'
            )
        );

        $apply = new SimpleQuestionset('apply', array(
            'Jak byste zapsali data do tabulky pomocí jazyka HTML? Uveďte vzorek kódu 2 řádky, 2 sloupce.',
            'Jak byste zapsali data do tabulky pomocí jazyka Markdown (dialekt [Multimarkdown](https://github.com/fletcher/MultiMarkdown/wiki/MultiMarkdown-Syntax-Guide))? Uveďte vzorek kódu 2 řádky, 2 sloupce.',
            'Vytvořte seznam textových řetězců, které začínají na malá i VELKÁ písmena, číslice i ostatní znaky (např. `#@$%` atp.). Seřaďte je pomocí tabulkového procesoru od nejmenšího po největší. Popište, jak je procesor seřadil (zda jsou čísla před písmeny a podobně). Připište v jakém prostředí (aplikaci) jste test provedli.'
        ));

        $entitiesDict = array(
            'kniha',
            'počítač',
            'student',
            'vyučovací předmět',
            'počítačový program',
            'databáze knih',
            'sociální síť',
            'knihovna',
            'zoologická zahrada',
            'muzeum',
            'univerzita'
        );
        
        $entityDescription = new SimpleQuestionset('apply');
        $entityDescription->addRandomizedQuestion('Vymyslete k entitě „%entita%“ 3 atributy, které budete zapisovat jako celé číslo, reálné číslo, text, datum/čas a logickou pravdu či nepravdu (tedy celkem 15 atributů).', array('entita' => $entitiesDict), 2);  


        $create1 = new SimpleQuestionset('create');
        $create1->addRandomizedQuestion('Vytvořte tabulku, která bude mít alespoň %rows% řádků (+ hlavička) a %cols% sloupců. Tabulka bude popisovat %rows% objektů, které mají podobné vlastnosti. V každém sloupci bude jeden atribut. Až budete mít tabulku vytvořenou, zamyslete se, jaký typ hodnot mají jednotlivé atributy. Data v tabulce mohou být reálná nebo smyšlená. Soustřeďte se na konzistenci dat.'
            . "\n\n"
            . '> Příklad: Tabulka automobilů, ve sloupcích např. výrobce (text), model (text), SPZ (šestimístný kód), barva (text, popř. kód barvy), počet míst k sezení (celé číslo), objem válců (celé číslo), rok výroby (čtyřmístné číslo).',
            array(
                'rows' => array(6, 7, 8, 9, 10),
                'cols' => array(6, 7, 8, 9, 10),
                'objects' => array(6, 7, 8, 9, 10)
            )
        );
        
        $randNums = array(6, 7);
        $create2 = new SimpleQuestionset('create');
        $create2->addRandomizedQuestion('Najděte kdekoli na internetu webovou stránku, na které bude tabulka dat, která bude mít aspoň %cols% sloupců a 100 řádků. Tabulku vložte do tabulkové procesoru tak, aby to byla čistá data bez formátování. Do komentáře vložte odkaz na stránku, ze které máte data. Stručně popište jak jste postupovali s převodem dat do tabulkového procesoru.',
            array(
                'cols' => $randNums
            )
        );
        
        $nameparts = array('křestní jméno', 'příjmení');
        $create3 = new SimpleQuestionset('create');
        $create3->addRandomizedQuestion('Ze [sdíleného souboru](https://goo.gl/9D2Rk1) si zkopírujte data do vašeho tabulkového procesoru. Odstraňte z něj duplicity, vyfiltrujte záznamy, které začínají na stejné písmeno jako vaše %name1% a ostatní vymažte. Ve druhém sloupci zvýrazněte vaší oblíbenou barvou záznamy, které začínají na písmeno jako vaše %name2%. (Pokud takové záznamy v seznamu nejsou, tak si vyberte jiné písmeno a napište to do komentáře.) Ve třetím sloupci zvýrazněte kladné záznamy zelenou výplní a záporné červenou výplní.'
            . "\n\n"
            . 'Své jméno do úkolu neuvádějte.'
            . "\n\n"
            . '> Všechny praktické úkoly uložte do jednoho sešitu, každý úkol na jeden list. Sešit uložte jako ukoly.xls a odevzdejte.',
            array(
                'name1' => $nameparts,
                'name2' => $nameparts
            )
        );
        
        $questions = array_merge(
            $terms->getQuestions(2),
            $understand->getQuestions(1),
            $analyze->getQuestions(1),
            $apply->getQuestions(1),
            $entityDescription->getQuestions(2),
            $create1->getQuestions(1),
            $create2->getQuestions(1),
            $create3->getQuestions(1)
        );

        return $questions;
    }
    
    public function getRubrics() 
    {
        return array(
            'Používá autor/ka zpracovaného úkolu správně a konzistentně termíny, které se týkají práce s tabulkovými daty?',
            'Jsou data v předposlední úloze vložena správně do tabulky?',
            'Jsou praktické úkoly zpracovány správně a odevzdány ve správné podobě? Na základě poslední praktické úlohy vymyslete jméno (či jeho část), která se dá odvodit ze zpracovaného úkolu.'
        );
    }

}
