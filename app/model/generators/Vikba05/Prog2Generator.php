<?php

namespace Model\Generator\Vikba05;

use Nette;
use Model\Generator\IGenerator;
use Model\Generator\SimpleQuestionset;

class Prog2Generator extends Nette\Object implements IGenerator
{
    
    public function getPreface() 
    {
        return "Zadání je tentokrát pro všechy velmi podobné, protože u většiny příkladů nejde o vědomostní otázku, ale o praktickou tvorbu skriptů. Pokud projdete počátečními zjednodušenými příklady, budete připraveni na závěrečný úkol, který by vám měl zabrat něco pod dvě hodiny. Hodně štěstí!\n\n*Pokud vám dnešní úkol bude připradat příliš hravý a málo praktický, těšte se na příští :)*";
    }

    public function getQuestions() 
    {
        $console = new SimpleQuestionset('apply', array(
            "**Najděte způsob, [jak ve svém prohlížeči spustit konzoli s chybami javascriptu](https://www.google.cz/search?q=how+to+open+javascript+error+console+in+my+browser) a otevřete si ji.** Postup vlastními slovy krátce shrňte do komentáře kódu.\n\nSpusťte kód a přepište poslední chybové hlášení do komentáře u kódu. Vysvětlete, v čem byl problém a jak chybové hlášení vede k jeho řešení (pokud chybě nerozumíte, využijte opět Google). Zhodnoťte, proč se daná chyba (ne)objevuje v hlášení samotného editoru (vedle čísel řádků).\n\n*Pokud se v dalších úkolech setkáte s nefunkčností vašeho kódu a prohlížeč zdánlivě nic nedělá, hledejte odpověď nejprve ve výpisu chyb a pracujte s chybovým hlášením v případné další diskusi.*"
        ));
        
        $consolePrefillDict = array(
            'alert(e);',
            'var = "x";',
            'var promenna = x;',
            'var if = "x";',
            'var do = "x";',
            'alert("text";',
            'var auto = ["neco", "neco jineho";',
            'var auto = {};'. "\n" . 'auto.start();',
        );
        $console->type = 'javascript';
        $console->prefill = $consolePrefillDict[array_rand($consolePrefillDict)] . "\n\n/*\n\t1) postup otevření chybové konzole:\n\n\t2) poslední chyba:\n\n\t3) vysvětlení chyby\n\n\t4) proč chyba je/není vidět přímo v editoru?\n\n*/\n";
        
        
        
        $play1 = new SimpleQuestionset('create', array(
           'Inspirujte se hříčkou předváděnou na semináři (viz převyplněný kód) a **vytvořte svou vlastní variantu, která bude vtipná** — principem musí zůstat to, že kód vyžaduje nějaké slovo, které pak využije k nějakému vtipu. Do komentáře vlastními slovy popište, jak pracuje funkce `prompt()`.' 
        ));
        $play1->type = 'javascript';
        $play1->prefill = 'var prijmeni = prompt("Napiš nějaké příjmení:");

alert(prijmeni + " smrdí!");

/* 
    prostor pro komentář
*/
';
        
        $operators = new SimpleQuestionset('apply', array(
            'Jaký je rozdíl mezi operátory `=` a `==`?',
            'Jak se v javascriptu zapisují operátory *relační* a operátor *přiřazení*?',
            'Najděte problém v *podmínkovém výrazu (uvnitř kulatých závorek)*: `if (jeHezky = true) { … }`.',
            'Popište, co je to v kontextu javascriptu *operátor přiřazení*.',
            'Popište, co jsou v kontextu javascriptu *relační operátory*.',
        ));
        
        
       $play2 = new SimpleQuestionset('create', array(
           'Vytvořte další podobnou hříčku jako v úkolu č. 2, ale **tentokrát využijte funkci `confirm()` a podmínkovou strukturu `if–else`**. Do komentáře vlastními slovy popište, jak pracuje funkce `confirm()` a jak v podmínkové struktuře pracujete s jejími návratovými hodnotami.' 
        ));
        $play2->type = 'javascript';
        $play2->prefill = "\n\n\n\n\n\n\n" . '/* 
    prostor pro komentář
*/
';    
        
        
        $regex = new SimpleQuestionset('apply', array(
            'Ještě jednou se vrátíme k první hříčce. Není třeba vymýšlet další obdobu, namísto toho ji budeme vylepšovat: **pomocí ověření regulárním výrazem zajistěte, aby uživatel skutečně zadával příjmení**. Přepisujte pouze obsah regulárního výrazu na třetím řádku (mezi lomítky).' . "\n\n" .
            'Přijatelným ověřením funkčnosti bude, když projdou následující řetězce: `"Novák"`, `"Střihavková Straková"` a `"Bröcklová"` a neprojdou následující řetězce: `"koala"`, `"90210"` a `"Sedmého dne Bůh odpočíval."`.' . "\n\n" .
            'Kód si průběžně spouštějte a zkoušejte, zda vše funguje, jak má. Roli značek `^` a `$` v předvyplněném regulárním výrazu si dohledejte a popište do komentáře.'
        ));
        $regex->type = 'javascript';
        $regex->prefill = 'var prijmeni = prompt("Napiš nějaké příjmení:");

if (/^.+$/.test(prijmeni)) {
    alert(prijmeni + " smrdí!");    
} else {
    alert("Smrdíš!");
}

/* 
    místo pro komentář
*/
';


        $object1 = new SimpleQuestionset('create', array(
            'Doplňte metodu objektu v proměnné `odstavec`, která umožní při zavolání změnit písmo (tzn. obsah proměnných, které písmo popisují). Měňte pouze tuto metodu, nikoli její parametry nebo jiné části kódu.' . "\n\n" .
            '(Tento úkol se velmi blíží způsobu, který můžete pracovat s podobou textů na webových stránkách a v jiných počítačem vytvářených textech.)'
        ));
        $object1->type = 'javascript';
        $object1->prefill = 'var odstavec = {};
odstavec.pismo = "Times New Roman";
odstavec.velikostPisma = "6pt";
odstavec.zmenPismo = function (pismo, velikostPisma) {
    // kód doplňte zde  
};

alert("Nejprve byl odstavec vysázen v písmu " + 
    odstavec.pismo +
    " velikosti " +
    odstavec.velikostPisma +
    "."
);

odstavec.zmenPismo("Comic Sans", "20pt");

alert("Po zavolání funkce zmenPismo() je odstavec vysázen v písmu " + 
    odstavec.pismo +
    " velikosti " +
    odstavec.velikostPisma +
    "."
);';
        
        $object2 = new SimpleQuestionset('apply', array(
            'Doplňte objektu v proměnné `auto` metodu `popojed()`, která ho posune o 20 km a vyčerpá 1 litr benzínu. Neměňte žádnou jinou část kódu a dejte pozor, aby průběžné hlášky byly správné. Kód si hned na začátku spusťte a pozorujte co se děje a pak ho postupně proměňujte. Pokud už auto nemá benzín, nemělo by popojet.'
        ));

        $object2->type = 'javascript';
        $object2->prefill = 'var auto = {};
auto.ujetaVzdalenost = 0; // v kilometrech
auto.zbyvaBenzinu = 2; // v litrech
auto.popojed = function() {
    // kód doplňte zde
};

alert("Auto je na začátku, zbývá " + auto.zbyvaBenzinu + " l benzínu.");

/* 
    toto je tzv. "cyklus for", který se provede 
    třikrát (pokud nedojde k jeho přerušení příkazem "break")
    více o cyklech si povíme příští týden
*/
for (i = 0; i < 3; i = i + 1) {
    
    var vychoziVzdalenost = auto.ujetaVzdalenost;
    
    auto.popojed();
    
    if (vychoziVzdalenost < auto.ujetaVzdalenost) {

        alert(
            "Auto popojelo, máme za sebou " + 
            auto.ujetaVzdalenost + 
            " km a zbývá " + 
            auto.zbyvaBenzinu + 
            " l benzínu."
        );

    } else {
        
         alert("Auto už nepopojelo, celková ujetá vzdálenost je " + auto.ujetaVzdalenost + ".");
         break;

    }
}';
        
        
        
        $game = new SimpleQuestionset('create', array(
            'Hříčkám není konec, ale dostáváme se dál. **Využijte všechno, co jste se naučil/a v předchozích příkladech a vytvořte *textovou počítačovou hru*,** která se uživatele po spuštění ptá na otázky a z odpovědí skládá něco zajímavého, provádí jej příběhem atp. 
            
Tvorba této hry by vám měla zabrat 90–120 minut (od návrhu konceptu až po realizaci), tak si na ní dejte záležet. Minimální hra musí splňovat několik podmínek (podmínky jsou zároveň *tak trochu* nápověda):

- zeptejte se alespoň na 5 věcí,
- hra musí mít více konců — některé se dají považovat za výhru, jiné za prohru (například: *hra se zeptá, jestli chci udělat něco hezkého: když odmítnu, prohraju* atp.)
- pokud v nějakém místě hra skončí, zobrazte **vždy** jako poslední hlášku nápis "KONEC"
- vše ukládejte jako vlastnosti objektu v proměnné `hra` (viz nápověda v předvyplněném kódu),
- využijte funkcí `prompt()`, `confirm()` a pokud chcete za úkol tři hvězdičky, tak i funkce `test()`,
- využijte podmínkové konstrukce,
- využijte skládání řetězců pomocí operátoru plus (`+`, např. `zjisteni1 + zjisteni2`; pokud chcete mezi slovy přidat mezeru, snadno ji vložíte literálem: `zjisteni1 + " " + zjisteni2`).

Nakonec nezapomeňte vyplnit komentáře na konci kódu — reflektujte jak hru, tak to, co vám tento úkol přinesl.

Pokud nevíte, jak taková textová hra může vypadat, podívejte se na [tento vzorový příklad](http://jsfiddle.net/wmh9vryw/1/) a případně si vygooglete nějaké jiné [textovky](http://google.com/search?q=textovky). **Vzorový příklad nekopírujte — vytvořte vlastní příběh i strukturu.**'
        ));
        $game->type = 'javascript';
        $game->prefill = 'var hra = {};
hra.spust = function() {
    // var hra.zjisteni1 = prompt("...");
};

hra.spust(); 
            
            
/* 
    zde popište základní prostředí hry 
    a proč jste si vybrali právě toto prostředí
    a příběh
*/        
            
/*
    zde popište, jaké pro vás bylo
    plnění tohoto úkolu
*/';      
        

        $questions = array_merge(
            $console->getQuestions(1),
            $play1->getQuestions(1),
            $operators->getQuestions(1),
            $play2->getQuestions(1),
            $regex->getQuestions(1),
            $object1->getQuestions(1),
            $object2->getQuestions(1),
            $game->getQuestions(1)
        );

        return $questions;
    }
    
    public function getRubrics() 
    {
        return array(
            'Jsou všechny skripty funkční?',
            'Jsou úkoly č. 6 a 7 splněny správně? Pokud ne, vysvětlete, kde je problém.',
            'Jsou naprogramované hříčky logické?',
            'Jsou naprogramované hříčky vtipné?',
            'Využívá autor/ka funkci `test()` v závěrečném příkladu? Pokud ne, velmi dobře si rozmyslete, zda za úkol chcete udělit 3 hvězdičky. Takové rozhodnutí případně pečlivě zdůvodněte.',
            'Jaké chyby či nedostatky mají zpracované úkoly? Předveďte, vysvětlete a podložte zdrojem, který autor/ka v odpovědích nezmiňuje. (V [každém programu je možné najít chybu](http://vtipy.cyberserver.cz/sest-programatorskych-zakonu1-v-kazdem-programu-je-alespon-jedna-chyba2/d4496.htm), takže záporná odpověď není přípustná.)'   
        );
    }

}
