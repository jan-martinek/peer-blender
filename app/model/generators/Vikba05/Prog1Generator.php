<?php

namespace Model\Generator\Vikba05;

use Nette;
use Model\Generator\IGenerator;
use Model\Generator\SimpleQuestionset;

class Prog1Generator extends Nette\Object implements IGenerator
{
    
    
    public function getPreface() 
    {
        return "Úvodů do programování v javascriptu a programování obecně je na [internetu mnoho](http://google.com/search?q=úvod+do+javascriptu). Podstatné je, že pro naši výuku nebudete potřebovat znalost ničeho jiného (HTML si jistě pamatujete, ale zatím ho nebudeme používat). Podstatné bude, abyste si našli a zažili základní termíny a syntaxi.\n\n"
        .   "Kód můžete spouštět přímo v Blenderu, takže nejlepším způsobem, jak se skriptování naučit, je experimentování. Když v kódu uděláte syntaktickou chybu, přímo v editoru se vám objeví varování — pokud nebudete vědět, jak si s nečím poradit, obraťte se na kolegy ve fóru.";
    }

    public function getQuestions() 
    {
        $play1 = new SimpleQuestionset('remember', array(
            "Bez přípravy si přečtěte následující kód a spusťte ho. Vyhledejte si, k čemu je funkce [alert v javascriptu](http://google.com/search?q=alert+javascript). Následně okomentujte, co daný kód dělá. Vaši odpověď vložte jako komentář kódu, jak je naznačeno."
        ));
        $play1->type = 'javascript';
        $play1->prefill = 'function umyj(zvire) {
    return "umytý " + zvire;
}

var naseZvire = "velbloud";
var vysledek = umyj(naseZvire);

alert(vysledek);

/* 
    okomentujte zde
*/';
        
        $dict = array(
            'literál',
            'název funkce',
            'řetězec',
            'proměnnou',
            'definici proměnné',
            'volání funkce',
            'atribut'
        );
        
        $identify = new SimpleQuestionset('apply');
        $identify->addRandomizedQuestion("Najděte v kódu z prvního úkolu **%thing%** a popište vlastními slovy k čemu slouží.",
            array('thing' => $dict), 2); 
        
        
        
        $numsDict = array(7, 9, 17, 19, 21, 24, 29);
        $animalsDict = array(
            'medvěd', 'netopýr', 'plch', 
            'sysel', 'tchoř', 'vlk', 'vrápenec', 'bobr', 
            'křeček', 'los', 'plšík', 'rejsek', 
            'rys', 'bělozubka', 'břehouš', 
            'bukač', 'bukáček', 'drop', 'dytík', 'chřástal', 
            'jeřáb', 'kolpík', 'kulík', 'luňák', 
            'mandelík', 'morčák'
        );
        $play2 = new SimpleQuestionset('remember');
        $play2->addRandomizedQuestion("Bez přípravy si přečtěte následující kód a spusťte ho.\n\nNásledně jej okomentujte: **popište, co kód dělá**, a **vysvětlete nesoulad** mezi číslovkou, která je na 11. řádku kódu uvedena dvakrát (nejprve slovně, a pak literálem v hranatých závorkách).\n\nVaši odpověď vložte jako komentář kódu, jak je naznačeno.\n\nKód pak ještě pozměňte tak, aby ve vyskakovacím okně bylo číslo **%num%** a vypisovaným zvířetem v tabulce byl **%animal%**.",
            array('num' => $numsDict, 'animal' => $animalsDict), 1); 
        $play2->type = 'javascript';
        $play2->prefill = "var zvirata = ['kočka', 'medvěd', 'netopýr', 'plch', 
               'sysel', 'tchoř', 'vlk', 'vrápenec', 'bobr', 
               'křeček', 'los', 'myšivka', 'plšík', 'rejsek', 
               'rys', 'vydra', 'bělozubka', 'veverka', 'břehouš', 
               'bukač', 'bukáček', 'drop', 'dytík', 'chřástal', 
               'jeřáb', 'koliha', 'kolpík', 'kulík', 'luňák', 
               'mandelík', 'morčák'];

var pocetZvirat = zvirata.length;

alert('počet zvířat: ' + pocetZvirat + '\\n' + 'třetí zvíře: ' + zvirata[2]);

/* 
    okomentujte zde
*/";
        
        
        
        $termsDict = array(
            '**programování**',
            '**program**',
            '**skriptování**',
            '**skript**',
            
            '**javascript**',
            '**programovací jazyk**',
            'jak snadno vygooglit něco o javascriptu',
            'jak snadno vygooglit něco o programování',
            'jak snadno vygooglit něco o skriptování',
            
            '**objektově orientované programování**',
            'pojem **třída** v kontextu objektově orientovaného programování',
            'pojem **metoda** v kontextu objektově orientovaného programování',
            'pojem **objekt** v kontextu objektově orientovaného programování',
            
            'pojem **funkce** v kontextu javascriptu',
            'pojem **proměnná** v kontextu javascriptu',
            'pojem **atribut** v kontextu funkcí v javascriptu',
            'pojem **návratová hodnota** v kontextu funkcí v javascriptu',
            'pojem **literál** v kontextu javascriptu',
            'pojem **operátor** v kontextu javascriptu',
            
            'pojem **syntaxe** v kontextu javascriptu',
            'pojem **klíčové slovo** v kontextu javascriptu'
        );
        $terms = new SimpleQuestionset('remember');
        $terms->addRandomizedQuestion('Popište *vlastními slovy* %term%. Ilustrujte popis alespoň třemi praktickými příklady a odkažte se při vysvětlení na zdroj, z něhož jste čerpali.',
            array('term' => $termsDict), 3); 



        
        $expressionsDict = array(
            'var jmenoZvirete = "Leonard";',
            'var vekOsoby = db.exec("SELECT vek FROM osoba WHERE id = " + idOsoby);',
            'var vysledek = pocetJablek + 5;',
            'var vysledek = pocetDni * 60 * 60 * 12;',
            'var jmenoAPrijmeni = "Martin" + " " + "Krčál";',
            'var auto = { barva: "červená", typ: "kombi", znacka: "Škoda" };',
            'var nakupniSeznam = ["toaletní papír", "mrkev"];'
        );
        $expressions = new SimpleQuestionset('remember');
        $expressions->addRandomizedQuestion("Popište *vlastními slovy* javascriptový výraz `%expression%`.\n\nPopište užívaný datový typ a vysvětlete, k čemu by takový kousek kódu mohl sloužit.",
            array('expression' => $expressionsDict), 2);



        $play3 = new SimpleQuestionset('apply');
        $play3->addRandomizedQuestion("Pozměňte kód tak, abyste zjistili, na kterém místě v poli se nachází **%animal%**.\n\nPotřebnou změnu popište do kódu. Popište, proč se na řádku 11 přičítá jednička.",
            array('animal' => $animalsDict), 1); 
        $play3->type = 'javascript';
        $play3->prefill = "var zvirata = ['kočka', 'medvěd', 'netopýr', 'plch', 
               'sysel', 'tchoř', 'vlk', 'vrápenec', 'bobr', 
               'křeček', 'los', 'myšivka', 'plšík', 'rejsek', 
               'rys', 'vydra', 'bělozubka', 'veverka', 'břehouš', 
               'bukač', 'bukáček', 'drop', 'dytík', 'chřástal', 
               'jeřáb', 'koliha', 'kolpík', 'kulík', 'luňák', 
               'mandelík', 'morčák'];

var zvire = 'kočka';

var poradi = zvirata.indexOf(zvire) + 1;

alert(zvire + ' je ' + poradi + '. v pořadí');

/* 
    okomentujte zde
*/";
    
        
        $play4 = new SimpleQuestionset('remember', array(
            "**Následující kód není složitý, je pouze dlouhý.** Popište vlastními slovy, co popisuje následující kód, na místech určených komentáři (datové typy na začátku a poté další komentáře níže).\n\nVšimněte si, že jde o popis — stejně jako jsme se s tím setkali u databází (anebo třeba ve slohovce na střední škole)."
        ));
        $play4->type = 'javascript';
        $play4->prefill = "// budeme popisovat auto
var auto = {}; // datový typ:

// popisujeme auto
auto.barva = 'červená'; // datový typ:
auto.dobaJizdy = 0; // datový typ:  
auto.pasazeri = ['Radek', 'Pavla']; // datový typ:
auto.rychlost = 0; // datový typ:

// definujeme metody, které pracují s proměnnými
auto.ujetaVzdalenost = function() {
    return auto.dobaJizdy * auto.rychlost;
};
auto.pocetPasazeru = function() {
    return auto.pasazeri.length;
};

// jak to vypadá?
alert('v autě sedí ' + auto.pocetPasazeru() + ' lidé');

// a jedeme
auto.dobaJizdy = 2;
auto.rychlost = 70;
alert('auto nejprve ujelo ' + auto.ujetaVzdalenost() + ' km');

/* 
    okomentujte, co se stalo na řádcích 22 a 23
    použijte termín \"proměnná\"
*/

// a jedeme dál
alert('a pak jelo ještě hodinu');
auto.dobaJizdy = auto.dobaJizdy + 1;
alert('celkem auto ujelo ' + auto.ujetaVzdalenost() + ' km');

/* 
    okomentujte, co se stalo na řádku 33
    použijte termín \"proměnná\"
*/

// a co se nestalo...
alert('a pak do auta nastoupil ještě Honza');
auto.pasazeri.push('Honza');
alert('v autě teď sedí ' + auto.pocetPasazeru() + ' lidé');

/* 
    okomentujte, co se stalo na řádku 43
    použijte termíny \"pole\", \"prvek\" a \"metoda\"
*/";
                     
        $questions = array_merge(
            $play1->getQuestions(1),
            $identify->getQuestions(2),
            $play2->getQuestions(1),
            $terms->getQuestions(3),
            $expressions->getQuestions(2),
            $play3->getQuestions(1),
            $play4->getQuestions(1)
        );

        return $questions;
    }
    
    public function getRubrics() 
    {
        return array();
    }

}
