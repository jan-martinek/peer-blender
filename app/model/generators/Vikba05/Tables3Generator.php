<?php

namespace Model\Generator\Vikba05;

use Nette;
use Model\Generator\IGenerator;
use Model\Generator\SimpleQuestionset;

class Tables3Generator extends Nette\Object implements IGenerator
{
    
    
    public function getPreface() 
    {
        return 'Praktické úkoly 4—7 řešte v jednom sešitu, každý úkol na jeden list. '
            . 'Výsledný soubor uložte jako `ukoly3.xls` a odevzdejte. '
            . 'V případě potřeby jej můžete zazipovat, ale nemělo by to být nutné.'
            . "\n\n"
            . '> Tip: [Sdílený soubor](https://goo.gl/q92YbV) si můžete stáhnout celý '
            . '(nebo si vytvořit vlastní kopii) a pracovat přímo v něm.';
    }

    public function getQuestions() 
    {
        
        $remember = new SimpleQuestionset('remember', array(
            'Jaké datum je reprezentované číslem 0 ve vašem tabulkovém procesoru? Uveďte datum a tabulkový procesor.',
            'Co znamená zkratka CSV v kontextu tabulkových souborů?',
            'Jaký je rozdíl mezi soubory typu XLS a CSV?'
        ));        
        
        $apply = new SimpleQuestionset('apply', array(
            'Roky se často zapisují pomocí [římských čísel](https://cs.wikipedia.org/wiki/%C5%98%C3%ADmsk%C3%A9_%C4%8D%C3%ADslice). Použijte např. funkci `ROMAN` (základní, klasický zápis) a zjistěte, který rok od 1 do 2015 je v zápisu pomocí římských čísel nejdelší (má nejvíce znaků). Využijte při hledání vzorce.' . "\n\n" . 'Do vstupního pole napište rok (pomocí arabských i římských číslic) a délku letopočtu v římských číslících. Stručně popište, jak jste k výsledku došli.'
        ));
        
        $create1 = new SimpleQuestionset('create', array(
            'Ze [sdíleného souboru](https://goo.gl/q92YbV) si do tabulkového procesoru zkopírujte list „Formátování čísel“.'
            . "\n\n"
            . 'List obsahuje 6 skupinek dat, které si jsou podobné. V levém sloupci je vždy číslo. V právem sloupci je stejné číslo jako vlevo. Vašim úkolem je číslo vpravo naformátovat tak, aby vypadalo jako podle vzoru (označeny zeleně). V prvním bloku je to přidání znaku procentu, v druhém bloku (PSČ) vložit mezeru mezi 3. a 4. číslici, atd.'
            . "\n\n"
            . 'K řešení *nepoužívejte* vzorce, řešte *pouze pomocí formátování*. Pokud se číslo vlevo změní, automaticky se změní i číslo vpravo (neplatí pro vzory, ty jsou zadané pevně).'
            . "\n\n"
            . 'Postup stručně popište.'
        ));
        
        $create2 = new SimpleQuestionset('create', array(
            'Ze [sdíleného souboru](https://goo.gl/q92YbV) si do tabulkového procesoru zkopírujte list „Formátování data“.'
            . "\n\n"
            . 'List obsahuje předvyplněnou tabulku. V první sloupci jsou zadaná data (podle [Gregoriánského kalendáře](https://cs.wikipedia.org/wiki/Gregori%C3%A1nsk%C3%BD_kalend%C3%A1%C5%99)). Vašim úkolem je v jednotlivých sloupcích data naformátovat tak, aby odpovídala podle vzoru (zvýrazněný zeleně). V této části nepoužívejte vzorce, pouze formátování.'
            . "\n\n"
            . 'Do buňky **A9** pak vložte nějaké vlastní datum a buňky **I9** dopište zajímavost, která se k tomuto datu váže. Poté v buňce **B11** vypočtěte počet let mezi dnešním datem a datem v buňce **A9** (zaokrouhlete na celé číslo dolů).'
            . "\n\n"
            . 'Pokud změníte datum v prvním sloupci, tak se automaticky změní všude v celém řádku. Pokud změníte datum v buňce **A9**, automaticky se přepočítá buňka **B11**.'
            . "\n\n"
            . 'Postup stručně popište a uveďte vzorec pro výpočet z buňky **B11**.'
        ));
        
        $create3 = new SimpleQuestionset('create', array(
            'Na serveru [datahub.io](http://datahub.io) najděte dataset (tabulkový soubor s daty), který je ve formátu CSV a přijde vám zajímavý. CSV soubor naimportujte do [sdíleného souboru](https://goo.gl/q92YbV) do listu „Import CSV“.'
            . "\n\n"
            . 'Pokud bude příliš velký, zkraťte ho na prvních 100 řádků.'
            . "\n\n"
            . 'Do komentáře vložte odkaz na původní CSV soubor. Stručně popište postup, proč jste si vybrali tento soubor a jak by se s ním dalo dále pracovat, k čemu by se data dala použít apod.'
        ));
        
        $create4 = new SimpleQuestionset('create', array(
            'Ze [sdíleného souboru](https://goo.gl/q92YbV) si do tabulkového procesoru zkopírujte list „E-maily“.'
            . "\n\n"
            . 'List obsahuje předvyplněnou tabulku, kde jsou v první sloupci emailové adresy. Vaším úkolem je pomocí vzorců rozdělit emailovou adresu na:'
            . "\n\n"
            . '- jméno (část před zavináčem)' . "\n"
            . '- doména (část za zavináčem)' . "\n"
            . '- doména nejvyššího řádu (část za poslední tečkou)'
            . "\n\n"
            . 'V tabulce jsou 3 vzory (zvýrazněny zeleně).'
        ));
        
        $questions = array_merge(
            $remember->getQuestions(2),
            $apply->getQuestions(1),
            $create1->getQuestions(1),
            $create2->getQuestions(1),
            $create3->getQuestions(1),
            $create4->getQuestions(1)
        );

        return $questions;
    }
    
    public function getRubrics() 
    {
        return array(
            'Používá autor/ka zpracovaného úkolu správně a konzistentně termíny, které se týkají práce s tabulkovými daty?',
            'Jsou všechny výpočty v tabulkách provedeny správně? Které jsou nedostatečné? Doporučte autorce/autorovi zdroje pro doplnění znalostí.',
            'Je z výsledku patrné, jaký postup autor/ka použil/a při výpočtech? Které operace by bylo možné provést jednodušeji? Jak by si autor/ka zpracovaného úkolu mohl usnadnit či zpřehlednit práci? (Uveďte konkrétní příklady.)'
        );
    }

}
