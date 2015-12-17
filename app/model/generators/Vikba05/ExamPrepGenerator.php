<?php

namespace Model\Generator\Vikba05;

use Nette;
use Model\Generator\IGenerator;
use Model\Generator\SimpleQuestionset;

class ExamPrepGenerator extends Nette\Object implements IGenerator
{
    
    public function getPreface() 
    {
        return "";
    }

    public function getQuestions() 
    {
        
        // 1+2 text understand x 2
        $text1 = new SimpleQuestionset('understand',
            array(
                'Popište vlastními slovy, co je to nepravá kurziva a proč ji někdo používá?',
                'Odpovězte vlastními slovy: jsou typografická pravidla univerzální po celém světě?',
                'Popište vlastními slovy jaké jsou užívány uvozovky v České republice a v sousedních zemích.',
                'Popište vlastními slovy, co je to kerning.',
                'Co je to střední výška písma? Proč je podstatná?',
                'Popište vlastními slovy, co umožňuje užívání HTML. (Popište, co umožňuje jak v rámci jedné stránky, tak mezi stránkami.)',
                'Popište vlastními slovy, k čemu slouží atributy `src` a `alt` u obrázku v HTML.',
                'Popište vlastními slovy rozdíl mezi seznamem značeným `<ol> … </ol>` a seznamem uzavřeným ve značkách `<ul> … </ul>` v HTML. Pro jaké seznamy je vhodný první a druhý typ seznamu?',
                'Popište vlastními slovy k čemu se používají regulární výrazy.',
                'Popište vlastními slovy, co jsou to zástupné znaky v aplikacích MS Word a OpenOffice Writer.',
                'Vypište běžné klávesové zkratky pro funkce *Najít* a *Najít a nahradit* v alespoň třech programech ve vašem operačním systému (uveďte, jaký operační systém používáte).'
            )  
        );
        
        // 3 typo + html + md create
        $html = array('html', 'coby validní HTML');
        $markdown = array('markdown', 've formátu Markdown');
        $format = rand(0,1) == 1 ? $html : $markdown;
        
        $text2 = new SimpleQuestionset('create',
            array(
                'Na webu KISKu jsou zveřejněny [důležité dokumenty](http://kisk.phil.muni.cz/cs/rejstriky/dulezite-dokumenty). Zpracujte jeden z níže vyjmenovaných dokumentů ' . $format[1] . '. Při zpracování dbejte na sémanticky správné využívání typografických a syntaktických prostředků. Vybírejte z následujících dokumentů:
                
- Antiplagiátorská politika KISKu
- Informace pro diplomanty – jaro 2015
- Nejčastější stylistické nedostatky v závěrečných pracích'    
            )
        );
        $text2->type = $format[0];
        
        // 4 regex apply
                
        $regexDict = array(
            'e-mailová adresu',
            'české telefonní číslo',
            'PSČ',
            'jméno a příjmení',
            'desetinné číslo',
            'odkaz zapsaný v markdownu',
            'báseň o třech strofách po čtyřech verších (řádcích)'
        );
        $regex = new SimpleQuestionset('apply');
        $regex->addRandomizedQuestion('Vytvořte regulární výraz, kterým můžete zkontrolovat, zda je obsahem řetězce %regex%. Doplňte vzorový řetězec tak, aby funkce vracela hlášku „OK“.', 
            array('regex' => $regexDict), 3);
        $regex->type = 'javascript';
        $regex->prefill = "var string = \" ... \";\n\nvar regex = /^ ... $/;\n\nif (regex.test(string)) alert(\"OK\");\nelse alert(\"Chyba\");";
        
        // 5 tables understand
        $functionsDict = array('SUMIF', 'COUNTIF', 'SUMPRODUCT', 'VLOOKUP', 'FIND', 'TRIM', 'INDEX', 'FLOOR', 'CEILING', 'MATCH');    
        $tables1Questions = array(
            'Definujte *vlastními slovy* v kontextu tabulkových procesorů co nejpřesněji pojem *entita* a vysvětlete, jak se k němu vztahují pojmy *objekt* a *atribut*.',
            'Co znamená symbol dolar (`$`) před zápisem buňky ve vzorci? Např. `=A1 + $D$1`. Jak byste tuto možnost využili?',
            'Jaký je rozdíl mezi vzorcem (formula) a funkcí (function)?'
        );
        foreach($functionsDict as $function) {
            $tables1Questions[] = 'Popište vlastními slovy funkci `%'.$function.'%` v kontextu tabulkových procesorů. Jaké má parametry a k čemu byste ji použili?';
        }
        $tables1 = new SimpleQuestionset('understand', $tables1Questions);
        
        // 6 tables create --> příloha
        $tables2 = new SimpleQuestionset('create', array(
            '*(Úkoly jsou shodné jako u lekce č. 2. V testu bude využit jiný spreadsheet.)* Ze [sdíleného souboru](https://goo.gl/n2blnx) si do tabulkového procesoru zkopírujte list “Klasifikační arch ZŠ”. Do sloupce “Vážený průměr” vypočítejte pro každého studenta vážený průměr (2 desetinná místa). Do sloupce navržená známka pak zaokrouhlete vážený průměr na nejbližší celé číslo. Použijte vzorce tak, aby když se změní zadaná hodnota známky nebo váha, tak se automaticky přepočítá i vážený průměr a navržená známka.'
            . "\n\n"
            . 'Postup stručně popište a vypište funkce, které jste použili.'
            . "\n\n"
            . '*Jména a známky jsou smyšlené.*'
            . "\n\n"
            . 'Pokud nevíte, jak se počítá vážený průměr, poradí Vám [např. Wikipedie](https://cs.wikipedia.org/wiki/V%C3%A1%C5%BEen%C3%BD_pr%C5%AFm%C4%9Br).',
            '*(Úkoly jsou shodné jako u lekce č. 2. V testu bude využit jiný spreadsheet.)* Ze [sdíleného souboru](https://goo.gl/n2blnx) si do tabulkového procesoru zkopírujte list “Akcie”. Do sloupce “Hodnota (Kč)” vypočítejte hodnotu akcií podle kurzu v tabulce vlevo. Do políčka “Celkem” pak hodnotu celého portfolia. Všechny hodnoty upravte na 2 desetinná místa (pomocí formátování).'
            . "\n\n"
            . 'Použijte vzorce tak, aby se automaticky přepočítala hodnota portfolia a jeho položek, když se změní počet akcií, stav kurzu nebo název společnosti v portfoliu..'
            . "\n\n"
            . 'Použijte funkci `SVYHLEDAT` (v angličtině `VLOOKUP`). Postup stručně popište a vypište funkce, které jste použili.'
            . "\n\n"
            . '*Jedná se o skutečný kurzovní lístek. Portfolio je smyšlené.*',
            '*(Úkoly jsou shodné jako u lekce č. 2. V testu bude využit jiný spreadsheet.)* Ze [sdíleného souboru](https://goo.gl/n2blnx) si do tabulkového procesoru zkopírujte list „Seznam zaměstnanců“.'
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
            . '*Instituce a jména jsou smyšlená.*',
            '*(Úkoly jsou shodné jako u lekce č. 2. V testu bude využit jiný spreadsheet.)* U seznamu žádostí do programu Erasmus ještě chvilku zůstaneme. Ze [sdíleného souboru](https://goo.gl/n2blnx) si do tabulkového procesoru zkopírujte list “Kód Erasmus+”. Vytvořte vzorec, který z kódu vyextrahuje rok podání žádosti. Dále vytvořte vzorec, který z kódu vyextrahuje kód státu. V tabulce je uveden příklad.'
            . "\n\n"
            . 'Úkol vypracujte tak, aby když se změní kód žádosti, automaticky se přepočítají jak rok, tak kód státu.'
            . "\n\n"
            . 'Postup stručně popište a vypište funkce, které jste použili.'
            . "\n\n"
            . '*Jedná se o skutečná data.*'
        ));

        // 7 db understand
        $db1Dict = array(
            'pojem *relační model*', 
            'pojem *objekt*', 
            'pojem *entita*', 
            'pojem *atribut*',
            'pojem *vztah mezi entitami*',
            'pojem *záznam*', 
            'pojem *tabulka*', 
            'pojem *hodnota*',
            'pojem *funkce*',
            'pojem *dotaz*', 
            'pojem *index*', 
            'pojem *id*', 
            'pojem *identifikátor*', 
            'pojem *cizí klíč*',
            'pojem *relace*',
            'pojem *data*',
            'pojem *řádek*',
            'pojem *sloupec*',
            'pojem *operátor*',
            'pojem *datový typ*',
            'pojem *podmínka*',
            'pojem *návrh databáze*',
            'pojem *relace 1:1*',
            'pojem *relace 1:N*',
            'pojem *relace M:N*',
            'pojem *spojování tabulek*',
            'pojem *cizí klíč*',
            'pojem *primární klíč*',
            'pojem *normalizace*',
            'pojem *třetí normální forma*',
            'hodnotu `NULL`',
            'pojem *agregace*',
            'pojem *agregační funkce*',
            'funkci `SUM`',
            'funkci `AVG`',
            'funkci `MIN`',
            'funkci `MAX`'
        );
        $db1 = new SimpleQuestionset('remember');
        $db1->addRandomizedQuestion('Popište vlastními slovy %term% v kontextu relačních databází. Ilustrujte popis alespoň třemi příklady použití.',
            array('term' => $db1Dict), 1);

        // 8 db create
        $dbDict = array(
            'zoo',
            'kaštan',
            'návštěva',
            'mandarinka',
            'sedačka',
            'spínač',
            'úvaha',
            'topol',
            'lupínek',
            'dítě',
            'hůl',
            'tanec',
            'bříza',
            'píšťala',
            'peklo',
            'špek',
            'ječmen',
            'les',
            'pero',
            'opona',
            'parapet',
            'raketa',
            'šachy',
            'brzda',
            'tkaničky',
            'kolečko',
            'kozel',
            'slabina',
            'klient',
            'dům',
            'stodola',
            'mrakodrap',
            'kontejner',
            'osa',
            'ampérmetr',
            'posel',
            'režisér',
            'jelen'
        );
        $db2 = new SimpleQuestionset('create');
        $db2->addRandomizedQuestion(
            'Pomocí papíru a tužky nebo webového nástroje [Gliffy](https://www.gliffy.com) **vytvořte schéma**, které popisuje objekt **„%object%“** pomocí alespoň pěti různých entit, každou s alespoň třemi atributy. Vyznačte vazby mezi entitami včetně jejich [kardinality](https://www.google.cz/search?q=kardinalita+datab%C3%A1ze). Stejně jako minule si uvědomujte, že jde o *popis*, který je selektivní — nikdy nepopíšete vše — takže to, jak sestavíte třídy entit závisí na účelu, který váš *ER model* plní. **Účel si předem vymyslete a popište do vstupního pole.**'
            . "\n\n"
            . "**Druhá část úkolu je vytvoření samotných tabulek** pomocí nástroje [*Adminer*](http://jan-martinek.com/tmp/db/?sqlite=) a databáze *SQLite*. Oba již trochu znáte z předchozích příkladů. Díky Admineru nebudete muset v tomto úkolu používat přímo SQL příkazů."
            . "\n\n"
            . "Na [přihlašovací stránce](http://jan-martinek.com/tmp/db/?sqlite=) se připojte do své vlastní databáze, která má název ve tvaru `UČO.db` (tedy např. `123456.db`). Poté pomocí odkazu `Create table` vytvořte tabulku pro každou z vašich entit — název entity bude názvem tabulky a každý atribut vytvoří sloupec v tabulce, pozor dejte na správný výběr datových typů. Nezapomeňte na identifikátory a propojení mezi tabulkami pomocí cizích klíčů (když sloupec nazvete jako *existující tabulku* s koncovkou \"\_id\" (tedy např. \"akcie\_id\", Adminer vám napoví)."
            . "\n\n"
            . "**Poté, co tabulky vytvoříte, zbývá poslední krok: plnění.** Do každé tabulek vyplňte 1 vzorový řádek (pomocí odkazu `New item`). Jak takové tabulky mohou vypadat znáte z původního příkladu se zeměmi a agenty. Po naplnění tabulek zkontrolujte, zda vaše schéma odpovídá výsledné databázi a vše odevzdejte do přílohy: "
            . "\n\n"
            . 'Výsledné schéma na papíru vyfoťte a nahrejte do přílohy nebo publikujte veřejně webovou verzi a odkaz zkopírujte do odpovědi. Výslednou databázi vyexportujte (odkaz `Export`, vyberte Output "plain" a Format "SQL") a vložte do odpovědi.',
            array('object' => $dbDict), 1);
        $db2->type = 'sql';
        
        // 9 prog understand
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
            'pojem **parametr** v kontextu funkcí v javascriptu',
            'pojem **návratová hodnota** v kontextu funkcí v javascriptu',
            'pojem **literál** v kontextu javascriptu',
            'pojem **operátor** v kontextu javascriptu',
            
            'pojem **syntaxe** v kontextu javascriptu',
            'pojem **klíčové slovo** v kontextu javascriptu'
        );
        $terms = new SimpleQuestionset('remember');
        $terms->addRandomizedQuestion('Popište *vlastními slovy* %term%. Ilustrujte popis alespoň třemi praktickými příklady a odkažte se při vysvětlení na zdroj, z něhož jste čerpali.',
            array('term' => $termsDict), 3);
  
        $dict = array(
                    'literál',
                    'název funkce',
                    'řetězec',
                    'proměnnou',
                    'definici proměnné',
                    'volání funkce',
                    'parametr'
                );
                $identify = new SimpleQuestionset('apply');
                $identify->addRandomizedQuestion("Najděte v kódu **%thing%** a popište do komentáře vlastními slovy k čemu slouží.",
                    array('thing' => $dict), 2); 
                $identify->type = 'javascript';
                $identify->prefill = 'function umyj(zvire) {
    return "umytý " + zvire;
}

var naseZvire = "velbloud";
var vysledek = umyj(naseZvire);

alert(vysledek);

/* 
    okomentujte zde
*/
';

        $prog1 = rand(0,1) == 1 ? $terms : $identify;    
        
        // 10 prog create
        $prog2 = new SimpleQuestionset('apply', array(
            'Doplňte objektu v proměnné `auto` metodu `popojed()`, která ho posune o 20 km a vyčerpá 1 litr benzínu. Neměňte žádnou jinou část kódu a dejte pozor, aby průběžné hlášky byly správné. Kód si hned na začátku spusťte a pozorujte co se děje a pak ho postupně proměňujte. Pokud už auto nemá benzín, nemělo by popojet.'
        ));

        $prog2->type = 'javascript';
        $prog2->prefill = 'var auto = {};
auto.ujetaVzdalenost = 0; // v kilometrech
auto.zbyvaBenzinu = 2; // v litrech
auto.popojed = function() {
    // kód měňte pouze zde
};

alert("Auto je na začátku, zbývá " + auto.zbyvaBenzinu + " l benzínu.");

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

        $questions = array_merge(
            $text1->getQuestions(2),
            $text2->getQuestions(1),
            $regex->getQuestions(1),
            $tables1->getQuestions(1),
            $tables2->getQuestions(1),
            $db1->getQuestions(1),
            $db2->getQuestions(1),
            $prog1->getQuestions(1),
            $prog2->getQuestions(1)
        );

        return $questions;
    }
    
    public function getRubrics() 
    {
        return array('Lekce není určena k peer-assessmentu.');
    }

}
