<?php

namespace Model\Generator\Vikba05;

use Nette;
use Model\Generator\IGenerator;
use Model\Generator\SimpleQuestionset;

class Prog3Generator extends Nette\Object implements IGenerator
{
    
    public function getPreface() 
    {
        return "Zadání je tentokrát pro všechy poměrně podobné, protože u většiny příkladů nejde o vědomostní otázku, ale o praktickou tvorbu skriptů. Pokud projdete počátečními zjednodušenými příklady, budete opět připraveni na závěrečný úkol, který by vám měl zabrat něco pod dvě hodiny. Hodně štěstí!";
    }

    public function getQuestions() 
    {
        $while = new SimpleQuestionset('apply', array('Nic negooglujte (sic!), spusťte si párkrát kód a popište vlastními slovy, jak pracuje cyklus `while`. 

Neomezujte svou představivost a popište, jaké nepříliš kreativní činnosti děláte a co by tak bylo možné pomocí cyklu `while` zautomatizovat (inspirace: *příkladem by mohla být automatická kontrola e-mailů — když se v mailu objeví kód kurzu VIKBA05, označí jej program vykřičníkem*).'));
        $while->type = 'javascript';
        $while->prefill = 'var pokracuj = true;

while (pokracuj) {
    alert("Uááá!");
    
    pokracuj = confirm("Chceš pokračovat?");
    
    if (pokracuj === false) {
        alert("OK, to je konec.");
        break;
    }
}

/*
    místo pro komentář
*/
';
        
        
        $while2 = new SimpleQuestionset('apply', array('Spusťte si párkrát kód a popište vlastními slovy, jak pracuje cyklus `while` s číselnou proměnnou v podmínce.'
        ));
        $while2->type = 'javascript';
        $while2->prefill = 'var i = 0;
while (i <= 10) {
    alert(i);
    i = i + 2;
}

/*
    místo pro komentář
*/     
';   
        
        $for = new SimpleQuestionset('apply', array('Opět nic negooglujte (sic!), spusťte si párkrát kód a popište vlastními slovy, jak pracuje cyklus `for`. V kódu je záměrně uveden i kód předchozího příkladu, abyste měli srovnání s cyklem `while`.'
        ));
        $for->type = 'javascript';
        $for->prefill = 'var i = 0;
while (i <= 10) {
    alert(i);
    i = i + 2;
}

for (var i = 0; i <= 10; i = i + 2) {
    alert(i);
}


/*
    místo pro komentář
*/
';
        
    
        $loopIntro = 'Napište pomocí některého cyklu krátký skript, který po spuštění ';
        $loop = new SimpleQuestionset('apply', 
            array($loopIntro . 'vypíše všechny násobky šesti až do čísla 828 (výsledek by měl být podobný jako "6, 12, 18…")',
                $loopIntro . 'vypíše všechny násobky sedmi až do 136. násobku (výsledek by měl být podobný jako "7, 14, 21…")',
                $loopIntro . 'bude postupně tvořit větu ze slov, které získá pomocí funkce `prompt()` a nechá toho až ve chvíli, kdy uživatel nechá pole prázdné (pro ukončení cyklu použijte příkaz `break`)',
                $loopIntro . 'se zeptá, jakým dnem začíná rok, a vypíše všechny lednové dny a ke každému uvede den v týdnu (výsledek by měl být podobný jako "1. po, 2. út, 3. st…")',
                $loopIntro . 'postupně přičítá k nule čísla, která zadává uživatel pomocí funkce `prompt()` dokud uživatel nezadá nulu (pro ukončení cyklu použijte příkaz `break`)'
            ));
        $loop->type = 'javascript';
        
        
        
        
        $forin = new SimpleQuestionset('apply', array('Nic negooglujte, spusťte si párkrát kód a popište vlastními slovy, jak pracuje cyklus `for…in`.'
        ));
        $forin->type = 'javascript';
        $forin->prefill = 'alert("(ukázka č. 1)");

var auto = { barva: "červená", znacka: "Škoda", typ: "kombi" };

for (var vlastnost in auto) {
    var hodnota = auto[vlastnost];
    alert("Vlastnost auta ‚" + (vlastnost) + "‘ má hodnotu ‚" + hodnota + "‘.");
}

alert("(ukázka č. 2)");

var tridaVeSkole = { 
    vlevo: {
        vpredu: "Pepík a Honzík", 
        uprostred: "Batman a Robin",
        vzadu: "Hastrman a Tatrman"
    }, 
    uprostred: {
        vpredu: "Martina a Tomášek",
        uprostred: "Laďka a Pavla",
        vzadu: "Ted a Barney"
    },
    vpravo: {
        vpredu: "Clark a Lois",
        uprostred: "Lojzík a Pepinka",
        vzadu: "Tarzan a Jane"
    } 
};

for (var rada in tridaVeSkole) {
    var deti = tridaVeSkole[rada]["vpredu"];
    alert(deti);
}

alert("(ukázka č. 3)");

var zviratka = ["muflon", "žirafa", "tuleň", "prase"];

for (var i in zviratka) {
    var zviratko = zviratka[i];
    alert("Zvířátko číslo " + i + " je " + zviratko + ".");
}

/*
    místo pro komentář
*/
';

    
        $forin2Dict = array(
            'It was the best of times, it was the worst of times, it was the age of wisdom, it was the age of foolishness, it was the epoch of belief, it was the epoch of incredulity, it was the season of Light, it was the season of Darkness, it was the spring of hope, it was the winter of despair.',
            'Once upon a time and a very good time it was there was a moocow coming down along the road and this moocow that was coming down along the road met a nicens little boy named baby tuckoo.',
            'Someone must have slandered Josef K., for one morning, without having done anything truly wrong, he was arrested.',
            'It is a truth universally acknowledged, that a single man in possession of a good fortune, must be in want of a wife.',
            'If you really want to hear about it, the first thing you\'ll probably want to know is where I was born, and what my lousy childhood was like, and how my parents were occupied and all before they had me, and all that David Copperfield kind of crap, but I don\'t feel like going into it, if you want to know the truth.',
            'One summer afternoon Mrs. Oedipa Maas came home from a Tupperware party whose hostess had put perhaps too much kirsch in the fondue to find that she, Oedipa, had been named executor, or she supposed executrix, of the estate of one Pierce Inverarity, a California real estate mogul who had once lost two million dollars in his spare time but still had assets numerous and tangled enough to make the job of sorting it all out more than honorary.',
            'I was born in the Year 1632, in the City of York, of a good Family, tho\' not of that Country, my Father being a Foreigner of Bremen, who settled first at Hull; He got a good Estate by Merchandise, and leaving off his Trade, lived afterward at York, from whence he had married my Mother, whose Relations were named Robinson, a very good Family in that Country, and from whom I was called Robinson Kreutznaer; but by the usual Corruption of Words in England, we are now called, nay we call our selves, and write our Name Crusoe, and so my Companions always call\'d me.'
        );



    
        $forin2 = new SimpleQuestionset('apply', array('Dohledejte a vlastními slovy popište, co dělá funkce split() a jaké parametry přijímá. Pak upravte parametr této funkce tak, aby výsledkem byla jednotlivá slova. Dohledejte na internetu autora textu a dopište ho do komentáře.'));
        $forin2->type = 'javascript';
        $forin2->prefill = 'var text = "' . $forin2Dict[array_rand($forin2Dict)] . '";

/*
    popis funkce split
*/

vety = text.split(",");

for (var poradi in vety) {
    alert(vety[poradi]);
}

/*
    autor textu
*/
';
        
        $game = new SimpleQuestionset('create', array('Podobně jako minule je poslední úkol kreativní. Protože už umíme pracovat s cykly, nebudeme se tolik trápit se závorkami. Druhou výhodou je, že díky práci s cyklem, který se opakuje pořád, se můžeme v příběhu vracet zpátky. Vyzkoušejte si drobnou hříčku, kterou máte předvyplněnou níže a všimněte si, že celá hra se zjednodušila na sérii tvrzení, které začínají `else if` — kód je dlouhý, ale přehledný.

Vytvořte svůj vlastní příběh. Opět by vám to mělo zabrat cca 2 hodiny. Stejně jako minule vás čeká několik podmínek, které musíte naplnit (podmínky jsou opět nápovědou):

- zpracujte aspoň osm míst, mezi kterými se dá přecházet,
- alespoň jednou se vraťte zpět na místo, kde už jste byli,
- hra musí mít více konců — některé se dají považovat za výhru, jiné za prohru (například: *hra se zeptá, jestli chci udělat něco hezkého: když odmítnu, prohraju* atp.)
- pokud v nějakém místě hra skončí, využijte příkazu `break`; pokud pokračuje, využijte příkaz `continue`, kterým se aktuální cyklus ukončí a začne nový,
- využijte vlastností hráče, který je uložený v proměnné `hrac` — využijte připraveného pole `hrac.taska`, do něhož můžete přidávat další hodnoty nebo je naopak odebírat (funkce `hrac.taska.push()`, `hrac.taska.pop()` apod.),
- využijte funkcí `prompt()`, `confirm()` a pokud chcete za úkol tři hvězdičky, tak i cyklu `for…in` (tip: možná máte spoustu věcí v tašce),
- využijte podmínkové konstrukce.

Nakonec nezapomeňte vyplnit komentáře na konci kódu — reflektujte jak hru, tak to, co vám tento úkol přinesl.

**Vzorový příklad nekopírujte — vytvořte vlastní příběh i strukturu. Pro vlastní hru využijte nanejvýš proměnné `hrac`.**'));
        $game->type = 'javascript';
        $game->prefill = 'var hrac = {};
hrac.jmeno = prompt("Jak se jmenuješ?");
hrac.vek = parseInt(prompt("Kolik máš let?"));
hrac.mesto = prompt("V jakém městě žiješ?", "Brno");
hrac.taska = ["hřeben", "mobil"];

var pozice = "doma";

while (true) {
    
    if (pozice == "doma") {
        pozice = prompt("Kam chceš jít? Zadej „nikam“, „ven“ nebo „omrknout maily“.");
        continue;
    }
    
    else if (pozice == "ven") {
        alert(hrac.mesto + " halí mlha. Vyšel jsi ven a je ti úzko.");
        if (confirm("Chceš jít na hřbitov?")) {
            pozice = "hřbitov";
            continue;
        } else {
            alert("Jsi na ulici.");
            pozice = "ulice";
            continue;
        }
    } 
  
    else if (pozice == "omrknout maily") {
        alert("Čteš maily a dosáhneš zenu.");
        alert("Zen je často právě tam, kde ho nehledáš.");
        break;
    }
  
    else if (pozice == "ulice") {
        if (confirm("Chceš vzít kámen, který leží na zemi?")) {
            hrac.taska.push("kámen");   
        }
        alert("Na ulici není co dělat, jdeš domů.");
        pozice = "nikam";
        continue;
    }
  
    else if (pozice == "servis") {
        alert("— o měsíc později —");
        alert("Pán za přepážkou říká: bude to 14 tisíc za nový displej a základní desku. A pětikačku za písmeno Q, které beztak skoro nepoužíváte.");
        alert("Na účtence je skutečně napsáno: " + hrac.jmeno);
        alert("„Kvůli pitomým databázím jsem napsal q mraky!“ křičíte na nebohého servisáka.");
        break;
    } 
  
    else if (pozice == "hřbitov") {
        alert("Přišel jsi na hřbitov a je tam krásně.");
        var vek = hrac.vek - 2;
        alert("Na jednom z hrobů je napsáno: " + hrac.jmeno + ", " + vek + " let.");
        alert("Zděšeně prcháš domů.");
        if (confirm("Chceš vzít kámen, který leží u cesty?")) {
            hrac.taska.push("kámen");   
        }
        pozice = "nikam";
        continue;
    } 
  
    else if (pozice == "nikam") {
        alert("Jsi doma a teď musíš dodělat úkol do Transformace dat.");
        if (hrac.taska.indexOf("kámen") != -1) {
            var pouzitKamen = confirm("V tašce máš kámen. Chceš s ním ze zlosti rozbít notebook?");
            if (pouzitKamen) {
                alert("Notebook je kaput. Ale ničemu to nepomohlo.");
                pozice = "servis";
                continue;
            } else {
                alert("Ok, je to v klidu. Pohodlně se usaď a pokračuj v tvorbě úkolu.");
            }
        }
        break;
    } 
  
    else {
        alert("Zadal/a jsi něco špatně! S takovou se nikam nedostaneš.");
        break;
    }
}
alert("KONEC");';

        $questions = array_merge(
            $while->getQuestions(1),
            $while2->getQuestions(1),
            $for->getQuestions(1),
            $loop->getQuestions(1),
            $forin->getQuestions(1),
            $forin2->getQuestions(1),
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
