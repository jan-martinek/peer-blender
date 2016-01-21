<?php

namespace Model\Generator\Vikba05;

use Nette;
use Model\Generator\IGenerator;
use Model\Generator\SimpleQuestionset;

class Exam2Generator extends Nette\Object implements IGenerator
{
    
    public function getPreface() 
    {
        return 'Zpracujte zadané úlohy za následujících podmínek: 

- vždy odpovídejte vlastními slovy,
- u každého úkolu dohledejte alespoň jeden důvěryhodný internetový zdroj, který obsahuje podklady pro zpracování dané úlohy (může jít o dokumentaci, článek, dotaz ze StackOverflow, heslo na Wikipedii atp.) a *nepochází* od autora z KISKu,
- u každého zdroje uveďte, proč jej považujete za důvěryhodný,
- pokud při odpovídání využijete více zdrojů, uveďte všechny.

Test můžete průběžně ukládat tlačítkem uložit pod poslední otázkou nebo pomocí symbolu diskety pod čísly úloh.';
    }

    public function getQuestions() 
    {
        
        // 1+2 text understand x 2
        $text1 = new SimpleQuestionset('understand',
            array(
                '`typografie` K čemu slouží typografická pravidla?',
                '`typografie` Co je to nepravá kurziva a proč se používá?',
                '`typografie` Uveďte několik příkladů (alespoň 3), jak se liší typografická pravidla v různých jazycích.',
                '`typografie` Jaké jsou užívány uvozovky **v České republice a v sousedních zemích**?',
                '`typografie` Co je to kerning? Odpověď doprovoďte alespoň třemi praktickými příklady chybného kerningu.',
                '`typografie` Co je to střední výška písma? Proč je podstatná?',
                '`html` Co umožňuje užívání HTML? Popište, co umožňuje jak v rámci jedné stránky, tak v rámci celé webové prezentace či internetu.',
                '`html` K čemu slouží atributy `src` a `alt` u obrázku v HTML?',
                '`html` Jaký je rozdíl mezi seznamem značeným `<ol> … </ol>` a seznamem uzavřeným ve značkách `<ul> … </ul>` v HTML? Pro jaké seznamy je vhodný první a druhý typ?',
                '`regulární výrazy` K čemu se používají regulární výrazy?',
                '`regulární výrazy` Co jsou to zástupné znaky v aplikacích MS Word a OpenOffice Writer?',
                '`regulární výrazy` Vypište běžné klávesové zkratky pro funkce *Najít* a *Najít a nahradit* v alespoň třech programech ve vašem operačním systému (uveďte, jaký operační systém používáte).'
            )
        );
        
        // 3 typo + html + md create
        $docs = array(
            '[Pokyny k zápisu do studia pro nově přijaté](http://www.phil.muni.cz/plonedata/wff/prijimaci-rizeni/2015_2016/zapis_9_2015.doc)',
            '[Pokyny k zápisu do studia pro bývalé studenty, absolventy a stávající studenty MU](http://www.phil.muni.cz/plonedata/wff/prijimaci-rizeni/2015_2016/zapis_studenti_MU_15.doc)',
            '[Pokyny k zápisu do studia pro zářijové maturanty](http://www.phil.muni.cz/plonedata/wff/prijimaci-rizeni/2015_2016/zapis_mat_zari_15.doc)',
            '[Pokyny nepřijatým uchazečům](http://www.phil.muni.cz/plonedata/wff/prijimaci-rizeni/2015_2016/prezkum_2015.doc)',
            '[Pozvánka k oborovému testu - Psychologie](http://www.phil.muni.cz/plonedata/wff/prijimaci-rizeni/2015_2016/Pozvanky%20OT/FF_MU_pozvanka_PS.doc)');
        
        $doc = $docs[rand(0,4)];
        
        $html = array('html', 'coby validní HTML (ověřte validátorem HTML5)');
        $markdown = array('markdown', 've formátu Markdown');
        $format = rand(0,1) == 1 ? $html : $markdown;
        
        $text2 = new SimpleQuestionset('create',
            array(
                '`' . $format[0] . '` Na webu Filozofické fakulty MU jsou publikovány [dokumenty, týkající se přijímacího řízení](http://www.phil.muni.cz/wff/home/prijimaci/bakalarske/index_html) ve formátu DOC. Zpracujte dokument ' . $doc . ' z loňského roku ' . $format[1] . '. Při zpracování dbejte na sémanticky správné využívání typografických a syntaktických prostředků.'    
            )
        );
        $text2->type = $format[0];
        
        // 4 regex apply
        $regexDict = array(
            'jakákoli e-mailová adresa',
            'jakékoli české telefonní číslo',
            'jakékoli PSČ',
            'jakékoli jméno a příjmení',
            'jakékoli desetinné číslo',
            'jakýkoli (webový) odkaz zapsaný pomocí markdownu'
        );
        $regex = new SimpleQuestionset('apply');
        $regex->addRandomizedQuestion('`regulární výrazy` Vytvořte nebo najděte na webu regulární výraz, kterým můžete poměrně spolehlivě zkontrolovat, zda je obsahem řetězce *%regex%*. Do proměnné `string` pak doplňte vzorový řetězec, s nímž bude ověření v závěru skriptu hlásit **OK**. **Do komentáře popište, co jednotlivé použité symboly v regulárním výrazu znamenají.**', 
            array('regex' => $regexDict), 3);
        $regex->type = 'javascript';
        $regex->prefill = "var string = \" ... \";\n\nvar regex = /^ ... $/;\n\nif (regex.test(string)) alert(\"OK\");\nelse alert(\"Chyba\");\n\n/* zde okomentujte vytvořený regulární výraz */";
        
        // 5 tables understand
        $functionsDict = array('SUMIF', 'COUNTIF', 'SUMPRODUCT', 'VLOOKUP', 'FIND', 'TRIM', 'INDEX', 'FLOOR', 'CEILING', 'MATCH');    
        $tables1Questions = array(
            '`tabulky` Definujte *vlastními slovy* v kontextu tabulkových procesorů co nejpřesněji pojem *entita* a vysvětlete, jak se k němu vztahují pojmy *objekt* a *atribut*.',
            '`tabulky` Co znamenají symboly dolaru (`$`) v zápisu buňky ve vzorci? Např. `=A1 + $D$1`. Jak byste tuto možnost využili?',
            '`tabulky` Jaký je rozdíl mezi vzorcem (formula) a funkcí (function)?'
        );
        foreach($functionsDict as $function) {
            $tables1Questions[] = '`tabulky` Popište vlastními slovy funkci `%'.$function.'%` v kontextu tabulkových procesorů. Jaké má parametry a k čemu byste ji použili?';
        }
        $tables1 = new SimpleQuestionset('understand', $tables1Questions);
        
        // 6 tables create --> příloha
        $tables2 = new SimpleQuestionset('create', array(
            '`tabulky` Ze [sdíleného spreadsheetu](https://docs.google.com/spreadsheets/d/1qA5gqGbgH70H5zD4lmRbOdAfr0cyoEICK7IX5DukSR8/edit#gid=0) si stáhněte či zkopírujte list „Výsledky předmětu“. Do sloupců `P` a `Q` vypočítejte celkové hodnocení a přiřaďte studentům výsledné hodnocení dle tabulky pomocí funkce `VYHLEDAT` (popř. `LOOKUP`; jako první parametr použijte výsledné hodnocení). U hodnocení známkou F nastavte červené pozadí pomocí podmíněného formátování.'
            . "\n\n"
            . ' Poté pomocí funkce najděte 1) **nejlepší bodový výsledek** a v dalším poli **jméno a příjmení** studenta či studentky s tímto výsledkem. (**Tip:** Pro spojení jména a příjmení můžete využít prázdný sloupec `R`, to vám pomůže i s následným využitím funkce  `SVYHLEDAT`, popř. `VLOOKUP`.)'
            . "\n\n"
            . 'Postup stručně popište a vypište funkce, které jste použili. Výsledek odevzdejte ve formátu XLS v příloze tohoto úkolu.'
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
        $db1->addRandomizedQuestion('`databáze` Popište vlastními slovy %term% v kontextu relačních databází. Ilustrujte popis alespoň třemi příklady použití.',
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
            '`databáze` Pomocí papíru a tužky **vytvořte schéma**, které popisuje objekt **„%object%“** pomocí alespoň **tří entit**, každou s alespoň **třemi atributy** s určením datového typu.'
            . "\n\n"
            . 'Ke každému atributu doplňte alespoň **tři možné hodnoty** (pokud to datový typ umožňuje) a vyznačte vazby mezi entitami včetně jejich [kardinality](https://www.google.cz/search?q=kardinalita+datab%C3%A1ze).'
            . "\n\n"
            . 'To, jak sestavíte třídy entit, závisí na účelu, který váš *ER model* plní. **Účel sestaveného schématu si proto předem vymyslete a popište do vstupního pole.** To vám pomůže i při vymýšlení jednotlivých entit. Schéma na papíru odevzdejte na konci testu.',
            array('object' => $dbDict), 1);
        
        // 9 prog understand
        $termsDict = array(
            '**programování**',
            '**program**',
            '**skriptování**',
            '**skript**',
            
            '**javascript**',
            '**programovací jazyk**',
            
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
        $terms->addRandomizedQuestion('`javascript` Popište %term%. Ilustrujte popis alespoň třemi praktickými příklady.',
            array('term' => $termsDict), 3);
  
        $dict = array(
                    'literál',
                    'název metody',
                    'řetězec',
                    'proměnnou',
                    'definici proměnné',
                    'volání metody',
                    'parametr'
                );
                $identify = new SimpleQuestionset('apply');
                $identify->addRandomizedQuestion("`javascript` Najděte v kódu **%thing%** a popište do komentáře vlastními slovy, **k čemu nejspíše slouží** funkce, která byla (s úpravami a zjednodušeními) převzata z kódu Peer Blenderu, **a jak pracují její jednotlivé pasáže**.",
                    array('thing' => $dict), 2); 
                $identify->type = 'javascript';
                $identify->prefill = 'function saveHomework(values, course, user) {
    
    homework = new Homework();
    homework.unit = course.unit;
    homework.assignment = course.assignment;
    homework.user = user;
    homework.submitted_at = Date.now();
    homework.edited_at = Date.now();
    
    if (values.attachment.exists()) {
        homework.attachment = this.saveHomeworkFile(
            values.attachment, 
            course.course.id,
            course.unit.id,
            user.id
        );
    }
    
    database.save(homework);
}

/* místo pro komentář */
';

        $prog1 = rand(0,1) == 1 ? $terms : $identify;    
        
         // 10 prog create
        $prog2a = new SimpleQuestionset('apply', array(
                '`javascript` Popište vlastními slovy *řádek po řádku* fungování následujícího skriptu a zjistěte, jaká je hodnota proměnných `moped.ujetaVzdalenost` a `moped.spotrebovanyBenzin` na konci skriptu a tyto hodnoty interpretujte. Popisujte průběh skriptu, ne obsah literálů. (Skript po spuštění nic viditelného nedělá – hodnoty si musíte na konci vypsat sami.)'
        ));
            
        $prog2a->type = 'javascript';
        $prog2a->prefill = 'var moped = {};
moped.ujetaVzdalenost = 0; // kilometry
moped.spotrebovanyBenzin = 0; // litry
moped.spotreba = 3.4; // litry na 100 kilometrů

while (moped.ujetaVzdalenost < 37) {
    moped.ujetaVzdalenost = moped.ujetaVzdalenost + 1;
    moped.spotrebovanyBenzin = moped.spotrebovanyBenzin + moped.spotreba / 100;
}

/* místo pro komentář, můžete využít i řádkových komentářů (uvozeny dvěma lomítky) */
';

        $prog2b = new SimpleQuestionset('apply', array(
            '`javascript` Popište vlastními slovy *řádek po řádku* fungování následujícího skriptu a zjistěte, jaká je hodnota proměnných `author.albumCount` a `author.lifespan` na konci skriptu a tyto hodnoty interpretujte. Popisujte průběh skriptu, ne obsah literálů. (Skript po spuštění nic viditelného nedělá – hodnoty si musíte na konci vypsat sami.)'
        ));

        $prog2b->type = 'javascript';
        $prog2b->prefill = 'var author = {};
author.name = "David Bowie";
author.born = new Date(1947, 1, 8);
author.deceased = new Date(2016, 1, 10);
author.albums = [ 
   "David Bowie", "Space Oddity", "The Man Who Sold the World", "Hunky Dory", 
   "The Rise and Fall of Ziggy Stardust and the Spiders from Mars", 
   "Aladdin Sane", "Pin Ups", "Diamond Dogs", "Young Americans", 
   "Station to Station", "Low", "\"Heroes\"", "Lodger", 
   "Scary Monsters (And Super Creeps)", "Let\\\'s Dance", "Tonight", 
   "Never Let Me Down", "Black Tie White Noise", "The Buddha of Suburbia", 
   "Outside", "Earthling", "Hours...", "Heathen", "Reality", "The Next Day", 
   "Blackstar" 
];
author.albumCount = author.albums.length;
author.lifespan = (author.deceased - author.born)/1000/60/60/24/365;

/* místo pro komentář, můžete využít i řádkových komentářů (uvozeny dvěma lomítky) */
';

        $prog2 = rand(0,1) == 1 ? $prog2a : $prog2b;

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
