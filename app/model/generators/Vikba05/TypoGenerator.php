<?php

namespace Model\Generator\Vikba05;

use Nette;
use Model\Generator\IGenerator;

class TypoGenerator extends Nette\Object implements IGenerator
{
    
    
    public function getPreface() 
    {
        return 'Při zpracování úkolů vycházejte ze zadané literatury a dohledávejte další informace na internetu.';
    }
    

    public function getQuestions() 
    {
        $assignedQuestions = array();
        
        $questions = array(
            'Co je to čtverčík, od čeho je odvozen a k čemu se používá?',
            'Co je to kurziva a jak se používá?',
            'Co jsou to kapitálky a jak s nimi pracujeme?',
            'Co jsou to minusky?',
            'Co jsou to verzálky?',
            'Jaký je rozdíl mezi *písmem* a *fontem*?',
            'Co je to nepravá kurziva a proč ji někdo používá?',
            'Jsou typografická pravidla univerzální po celém světě?',
            'Jsou typografická pravidla univerzální alespoň tam, kde se používá latinkové písmo?',
            'Kdy používáme polotučný řez písma a k čemu?',
            'Kdy používáme tučné písmo a k čemu?',
            'Jak se liší spojovník a pomlčka? Jak napíšete oba znaky na klávesnici ve vašem operačním systému?',
            'Co jsou to aposiopese a výpustky?',
            'Podle čeho se říká běžným uvozovkám v českém kontextu „99 66“?',
            'Co je to kerning? Prožili jste v poslední době nějaký problém s kerningem ve svém běžném životě?',
            'Co je to střední výška písma? Proč je podstatná? (toto není v literatuře)',
            'Jak a proč se užívá zavěšená interpunkce?'
        );
        
        shuffle($questions);
               
        for ($i = 1; $i <= 2; $i++) {
            $assignedQuestions[] = array_pop($questions);  
        }
        
        $assignedQuestions[] = 'Najděte na *svém oblíbeném webu* článek o více než 1000 slovech a uveďte 
jej do co nejlepšího souladu s typografickými pravidly. Využijte přitom pouze 
běžný kancelářský textový procesor (Microsoft Word, Google Docs, OpenOffice Writer a ekvivalentní, 
nikoli Poznámkový blok a jiné plaintextové editory).
        
Výsledek přiložte coby soubor ve formátu PDF, DOC nebo ODT (pole pro přikládané soubory najdete níže). Ve vstupním poli popište, jaká pravidla
jste museli zanedbat kvůli omezením použitého softwaru, popř. z jiných důvodů.';
        
        return $assignedQuestions;
    }
    
    public function getRubrics() 
    {
        return array(
            'Jsou odpovědi na faktické otázky správné?',
            'Je praktický úkol správně zpracovaný? (Dostatečná délka textu, správné využití typografických pravidel.) Co by se dalo zlepšit?',
            'Ohodnoťte, jak si autor/ka úkolu uvědomuje omezení daná užitým softwarem. (Popište několika větami.)'
        );
    }

}
