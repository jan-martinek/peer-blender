<?php

namespace Model\Generator\Vikba05;

use Nette;
use Model\Generator\IGenerator;
use Model\Generator\SimpleQuestionset;

class Tables2Generator extends Nette\Object implements IGenerator
{
    
    
    public function getPreface() 
    {
        return 'Dejte si pozor na správné užívání pojmů.'
            . "\n\n"
            . 'Praktické úkoly (4&ndash;8) řešte v jednom sešitu, každý úkol na jeden list. '
            . 'Výsledný soubor uložte jako `ukoly2.xls` a odevzdejte. V případě potřeby jej můžete zazipovat, '
            . 'ale nemělo by to být nutné. Důležité je najít správný výsledek a využít přitom vhodné funkce — '
            . 'ne to, jak výsledek vypadá.'
            . "\n\n"
            . 'Sdílený soubor si s podklady najdete [zde](https://goo.gl/n2blnx). **Tip:** můžete si jej stáhnout celý '
            . '(nebo si vytvořit vlastní kopii) a pracovat přímo v něm.';
    }

    public function getQuestions() 
    {
        $functionsDict = array(
            'SUMIF', 
            'COUNTIF', 
            'SUMPRODUCT', 
            'VLOOKUP', 
            'FIND', 
            'TRIM', 
            'INDEX', 
            'FLOOR', 
            'CEILING', 
            'MATCH'
        );
        
        $functions = new SimpleQuestionset('analyze');
        $functions->addRandomizedQuestion('Popište funkci `%fn%`. Jaké má parametry a k čemu byste ji použili?', 
            array('fn' => $functionsDict), 2);  
        
        $remember = new SimpleQuestionset('remember', array(
            'Co znamená symbol dolar (`$`) před zápisem buňky ve vzorci? Např. `=A1 + $D$1`. Jak byste tuto možnost využili?',
            'Jaký je rozdíl mezi vzorcem (formula) a funkcí (function)?'
        ));
        
        $create1 = new SimpleQuestionset('create', array(
            'Ze [sdíleného souboru](https://goo.gl/n2blnx) si do tabulkového procesoru zkopírujte list “Klasifikační arch ZŠ”. Do sloupce “Vážený průměr” vypočítejte pro každého studenta vážený průměr (2 desetinná místa). Do sloupce navržená známka pak zaokrouhlete vážený průměr na nejbližší celé číslo. Použijte vzorce tak, aby když se změní zadaná hodnota známky nebo váha, tak se automaticky přepočítá i vážený průměr a navržená známka.'
            . "\n\n"
            . 'Postup stručně popište a vypište funkce, které jste použili.'
            . "\n\n"
            . '*Jména a známky jsou smyšlené.*'
            . "\n\n"
            . 'Pokud nevíte, jak se počítá vážený průměr, poradí Vám [např. Wikipedie](https://cs.wikipedia.org/wiki/V%C3%A1%C5%BEen%C3%BD_pr%C5%AFm%C4%9Br).'
        ));
        
        $create2 = new SimpleQuestionset('create', array(
            'Ze [sdíleného souboru](https://goo.gl/n2blnx) si do tabulkového procesoru zkopírujte list “Akcie”. Do sloupce “Hodnota (Kč)” vypočítejte hodnotu akcií podle kurzu v tabulce vlevo. Do políčka “Celkem” pak hodnotu celého portfolia. Všechny hodnoty upravte na 2 desetinná místa (pomocí formátování).'
            . "\n\n"
            . 'Použijte vzorce tak, aby se automaticky přepočítala hodnota portfolia a jeho položek, když se změní počet akcií, stav kurzu nebo název společnosti v portfoliu..'
            . "\n\n"
            . 'Použijte funkci `SVYHLEDAT` (v angličtině `VLOOKUP`). Postup stručně popište a vypište funkce, které jste použili.'
            . "\n\n"
            . '*Jedná se o skutečný kurzovní lístek. Portfolio je smyšlené.*'
        ));
        
        $create3 = new SimpleQuestionset('create', array(
            'Ze [sdíleného souboru](https://goo.gl/n2blnx) si do tabulkového procesoru zkopírujte list „Seznam zaměstnanců“.'
            . "\n\n"
            . 'Pomocí vzorců vypočítejte hodnoty:'
            . "\n\n"
            . '- Počet neproškolených BZOP,' . "\n"
            . '- Celková prémie pro knihovníky,' . "\n"
            . '- Průměrná prémie pro knihovníky,' . "\n"
            . '- Příjmení zaměstnance s nejvyšší prémií.'
            . "\n\n"
            . 'Použijte vzorce tak, aby když se změní pozice, informace o proškolení nebo výše prémie, tak se hodnoty automaticky přepočítají. Hodnoty o prémiích naformátujte na 2 desetinná místa + symbol „Kč“.'
            . "\n\n"
            . 'Postup stručně popište a vypište funkce, které jste použili.'
            . "\n\n"
            . '*Instituce a jména jsou smyšlená.*'
        ));
        
        $create4 = new SimpleQuestionset('create', array(
            'Ze [sdíleného souboru](https://goo.gl/n2blnx) si do tabulkového procesoru zkopírujte list “Erasmus+” (obsahuje pouze hlavičku). Na internetu vyhledejte seznam zemí Evropského hospodářského prostoru a vložte je do prvního sloupce (názvy zemí česky nebo anglicky). Evropskou unii jako celek do seznamu zahrnovat nemusíte. Dohledejte k nim kód země. '
            . "\n\n"
            . 'Stáhněte si [soubor podaných žádostí o zapojení do programu Erasmus+](https://is.muni.cz/auth/el/1421/podzim2015/VIKBA05/um/eche-2014-updated-list-of-awarded-applications-011014.xls). Pomocí tohoto seznamu spočítejte, kolik bylo žádajících institucí z každé země.'
            . "\n\n"
            . 'Postup stručně popište a vypište funkce, které jste použili.'
            . "\n\n"
            . '*Jedná se o skutečná data.*'
        ));
        
        $create5 = new SimpleQuestionset('create', array(
            'U seznamu žádostí do programu Erasmus ještě chvilku zůstaneme. Ze [sdíleného souboru](https://goo.gl/n2blnx) si do tabulkového procesoru zkopírujte list “Kód Erasmus+”. Vytvořte vzorec, který z kódu vyextrahuje rok podání žádosti. Dále vytvořte vzorec, který z kódu vyextrahuje kód státu. V tabulce je uveden příklad.'
            . "\n\n"
            . 'Úkol vypracujte tak, aby když se změní kód žádosti, automaticky se přepočítají jak rok, tak kód státu.'
            . "\n\n"
            . 'Postup stručně popište a vypište funkce, které jste použili.'
            . "\n\n"
            . '*Jedná se o skutečná data.*'

        ));
        
        $questions = array_merge(
            $functions->getQuestions(2),
            $remember->getQuestions(1),
            $create1->getQuestions(1),
            $create2->getQuestions(1),
            $create3->getQuestions(1),
            $create4->getQuestions(1),
            $create5->getQuestions(1)
        );

        return $questions;
    }
    
    public function getRubrics() 
    {
        return array(
            'Používá autor/ka zpracovaného úkolu správně a konzistentně termíny, které se týkají práce s tabulkovými daty?',
            'Jsou všechny výpočty v tabulkách provedeny správně? Které jsou nedostatečné? Doporučte autorce/autorovi zdroje pro doplnění znalostí.',
            'Je z výsledku patrné, jaký postup autor/ka použil/a při výpočtech?'
        );
    }

}
