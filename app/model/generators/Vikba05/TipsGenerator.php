<?php

namespace Model\Generator\Vikba05;

use Nette;
use Model\Generator\IGenerator;
use Model\Generator\SimpleQuestionset;

class TipsGenerator extends Nette\Object implements IGenerator
{
    
    public function getPreface() 
    {
        return "Všechny úkoly splňte v následujícím rozsahu (pokud je to aplikovatelné):
        
1. Najděte na webu popis či postup platný pro vaši platformu,
2. reprodukujte postup na vlastním počítači či jiném zařízení (pokud jej nevlastníte, využijte pomoci přátel),
3. vlastními slovy popište postup tak, aby jej dokázal splnit člověk s nízkou úrovní počítačové gramotnosti: specifikujte platformu, popište přesný postup a použitý software (příp. webové služby), pokud je to vhodné, ilustrujte popis obrázky, které přiložte k úkolu,
4. doplňte odpovědi na případné další otázky v zadání.";
    }

    public function getQuestions() 
    {
        
        $tips = new SimpleQuestionset('understand',
            array(
                'Jak udělat otisk (snímek) obrazovky a poslat jej mailem?',
                'Jak udělat otisk (snímek) obrazovky na mobilním zařízení (smartphone či tablet)?',
                'Jak rozbalit soubory z archivu ZIP?',
                'Jak rozbalit soubory z archivu RAR?',
                'K čemu slouží archivování souborů? Proč se u souborů typu JPEG při archivaci velikost příliš nesnižuje?',
                'Jaké máte možnosti při zmenšování velikosti souboru s fotografií, aby se vešel např. do přílohy v Blenderu?',
                'Jak procházet rychle textem pomocí klávesových zkratek? (Skok po znacích, slovech, řádcích, označování pomocí klávesnice, posunování odstavců v dokumentu atp.)',
                'Jak uložit obrázek s otiskem obrazovky tak, aby nedošlo ke ztrátě obrazové kvality?',
                'Jak uložit fotku tak, aby měl výsledný soubor co nejmenší velikost?',
                'Jak oříznout obrázek, aby bylo zobrazeno pouze to podstatné?',
                'Jaké jsou možnosti při hledání souborů na disku počítače? (Vysvětlete rozdíl mezi hledáním v indexu a v souborech.)',
                'Jak najít text ve stránce při prohlížení internetu v internetovém prohlížeči?',
                'Jak najít text ve zdrojovém kódu stránky při prohlížení v internetovém prohlížeči?',
                'Jak pomocí Google převádět světové měny?',
                'Jak využít vyhledávací pole Google jako kalkulačku?',
                'Jaké všechny operátory vyhledávání podporuje [Google](http://google.com)? (Uveďte příklady využití.)',
                'Jaké všechny operátory vyhledávání podporuje [Bing](http://bing.com)? (Uveďte příklady využití.)',
                'V čem spočívají výhody internetového vyhledávače [DuckDuckGo](https://duckduckgo.com)?',
                'Jaké nástroje pro vyhledávání podporuje hledání v obrázcích [Google](http://google.com)?',
                'Jak lze nejsnadněji napsat speciální znaky *spojovník, pomlčka, dlouhá pomlčka, křížek (#), tilda (~), stříška (^), ampersand (&), hranaté a složené závorky, středník a české uvozovky*?',
                'Jak zapnout anonymní režim prohlížeče? K čemu je tento režim vhodný (uveďte alespoň tři příklady)?',
                'Vyberte si nějaký software pro automatizované zálohování a popište jeho instalaci a nastavení. Jaké jsou výhody a nevýhody různých způsobů zálohování?',
                'Vyberte si nějaký software pro správu hesel a popište jeho instalaci a nastavení. K čemu je možné využít software pro správu hesel? V čem spočívají výhody a nevýhody?',
                'Vyberte si nějakou RSS čtečku a popište její instalaci (resp. registraci v případě online služby) a nastavení. K čemu je možné využívat RSS čtečku?',
                'Popište, k čemu jsou vhodné různé formáty obrázků (alespoň JPEG, GIF, PNG, TIFF, BMP a RAW).',
                'Jak se liší bitmapová a vektorová grafika? Uveďte několik příkladů grafických editorů pro oba typy grafiky.',
                'Jak korektně odinstalovat software?',
                'Jaké jsou zásady pro tvorbu bezpečného hesla?',
                'Popište, jak je možné ve vašem e-mailovém klientu, aplikaci či mobilní službě automatizovaně třídit došlou poštu.',
                'Popište, jak pomocí jedné aplikace či online služby spravovat více e-mailových adres (tzn. přijímat i odesílat poštu na více adresách).',
                'Jaké služby je možné využít pro vytvoření dočasné e-mailové schránky?',
                'Jaké klávesové zkratky jsou nejdůležitější ve vašem operačním systému? Najděte na webu cheat-sheet pro rychlou referenci.',
                'Jaké klávesové zkratky jsou nejdůležitější ve vašem oblíbeném textovém procesoru (MS Word a ekvivalentní)? Najděte na webu cheat-sheet pro rychlou referenci.',
                'Jaké klávesové zkratky jsou nejdůležitější ve vašem oblíbeném internetovém prohlížeči? Najděte na webu cheat-sheet pro rychlou referenci.',
                'Jaké klávesové zkratky jsou nejdůležitější v textovém editoru kódu (Brackets a ekvivalentní)? Najděte na webu cheat-sheet pro rychlou referenci.',
                'Jak otevřít poslední zavřenou záložku a okno v internetovém prohlížeči?',
                'Jak nalézt nápovědu a kontaktovat podporu pro váš operační systém?',
                'Jak ve vašem oblíbeném textovém procesoru (MS Word a ekvivalentní) vygenerovat obsah s čísly stránek?',
                'Jak ve vašem oblíbeném textovém procesoru (MS Word a ekvivalentní) vygenerovat rejstřík?',
                'Jak ve vašem oblíbeném textovém procesoru (MS Word a ekvivalentní) vygenerovat seznam všech obrázků?',
                'Jak automatizovaně spravovat citace ve vašem oblíbeném textovém procesoru (MS Word a ekvivalentní)?',
                'Jak automatizovaně spravovat citace pomocí online služby?',
                'Jak převést soubor mezi formáty ODT, DOCX, DOC a RTF? Jaké jsou výhody a nevýhody jednotlivých formátů?',
                'Jak procházet pomocí klávesových zkratek mezi spuštěnými aplikacemi tam a zpět?',
                'Jak získat podrobné informace o souboru?',
                'Jak získat podrobné informace o dokumentu (jméno autora, datum posledních úprav atp.) v textovém procesoru (MS Word a ekvivalentní)?',
                'Jak získat podrobné informace o fotografii (EXIF)? Popište jednotlivé parametry uložené v metadatech.',
                'Jaké klávesové zkratky jsou na platformě Windows spojeny s klávesou `Okno`?',
                'Jak vytvořit zástupce souboru?',
                'Co je to souborový systém? Jak se liší různé souborové systémy (FAT, FAT32, exFAT, NTFS, HFS)?',
                'Jak otevřít příkazovou řádku? Popište několik základních příkazů (alespoň 5) pro práci se soubory.',
                'Jak otevřít příkazovou řádku? Popište několik základních příkazů (alespoň 5) pro práci s počítačovou sítí a internetem.',
                'Popište, co je to kódování textu a jaká kódování jsou relevantní v českém prostředí v současné době.',
                'Jak spočítat počet slov a odstavců v textu?',
                'Proč existují různá rozložení klávesnice? Jaká rozložení klávesnice jsou užitečná pro českého uživatele a proč?',
                'Jak zjistit velikost souborů a složek? Najděte nějakou aplikaci pro vizualizaci obsazeného místa na disku počítače a popište práci s ní.',
                'Jak smazat historii v internetovém prohlížeči? Jak smazat dočasné soubory, cookies a další soubory spojené s konkrétní navštívenou stránkou či serverem?',
                'Jak udržet počítač dlouhodobě stabilní a rychlý?',
                'Jak najít levné alternativy profesionálního software? Jaké jsou výhody a nevýhody užívání levných alternativ?',
                'Co jsou to otevřené datové formáty? Uveďte příklady těchto formátů a rozdíly oproti proprietárním formátům.',
                'Co je to software s otevřeným kódem a jaké jsou jeho výhody a nevýhody?',
                'Jaké jsou možnosti online editace obrázků (popište konkrétní služby a krátce i jejich funkcionalitu, uveďte alespoň 5 příkladů)?'
            )  
        );
        
        $questions = array_merge(
            $tips->getQuestions(6)
        );

        return $questions;
    }
    
    public function getRubrics() 
    {
        return array('Jsou otázky zodpovězeny správně a kompletně? (Pokud je to aplikovatelné, odpovídají zadání v záhlaví?)',
            'Odhadněte: Byly by odpovědi srozumitelné pro vaši babičku?',
            'Zhodnoťte, která z odpovědí vám přinesla nejvíce nových poznatků.');
    }

}
