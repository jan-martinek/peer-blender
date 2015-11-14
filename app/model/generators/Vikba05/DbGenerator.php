<?php

namespace Model\Generator\Vikba05;

use Nette;
use Model\Generator\IGenerator;
use Model\Generator\SimpleQuestionset;

class DbGenerator extends Nette\Object implements IGenerator
{
    
    
    public function getPreface() 
    {
        return '';
    }

    public function getQuestions() 
    {
        $playDict = array(
            'SELECT * FROM countries WHERE area <= 400000 AND population > 100000000',
            'SELECT * FROM countries WHERE population_growth >= 3',
            'SELECT * FROM countries WHERE area <= 400000 AND population < 10000',
            'SELECT * FROM countries WHERE area >= 400000 AND code LIKE "a%"',
            'SELECT * FROM countries WHERE area >= 400000 AND code LIKE "%a"',
            'SELECT * FROM countries WHERE area < 400000 AND code LIKE "%a"',
            'SELECT * FROM countries WHERE name LIKE "%uk%" AND code LIKE "%a"',
            'SELECT * FROM countries WHERE name LIKE "%uni%" AND code LIKE "u%"',
            'SELECT * FROM countries WHERE name LIKE "United%" AND code LIKE "u%"',
            'SELECT * FROM countries WHERE name LIKE "United%" AND area < 500000',
            'SELECT * FROM countries WHERE name LIKE "C%" AND code LIKE "C%"'
        );        
        $play = new SimpleQuestionset('apply');
        $play->addRandomizedQuestion('Bez toho, že byste si příliš pročítali materiály, si otevřete [zkušební aplikaci](http://jan-martinek.com/tmp/db/?sqlite=&username=&db=factbook.db&sql=) a vložte do pole pro SQL příkaz tento příkaz: `%select%`. Popište, co obsahuje výsledná tabulka (pokud se objeví chyba, nejspíše jste příkaz zkopírovali špatně — nevíte-li si rady, optejte se na fóru).' . "\n\n" . 'Zkuste měnit hodnoty v příkazu (čísla, kódy států) a sledujte, jak se tabulka mění poté, co pozměněný příkaz spustíte (pomocí tlačítka *Execute*).',
            array('select' => $playDict), 1);
        
        $termsDict = array(
            'relační model', 
            'objekt', 
            'entita', 
            'atribut',
            'vztah mezi entitami',
            'záznam', 
            'tabulka', 
            'hodnota',
            'funkce',
            'dotaz', 
            'index', 
            'id', 
            'identifikátor', 
            'cizí klíč',
            'relace',
            'data',
            'řádek',
            'sloupec',
            'operátor',
            'datový typ',
            'podmínka',
            'datový typ',
            'návrh databáze'
        );
        $terms = new SimpleQuestionset('remember');
        $terms->addRandomizedQuestion('Popište vlastními slovy pojem *%term%* v kontextu relačních databází. Ilustrujte popis alespoň třemi příklady.',
            array('term' => $termsDict), 2);

            
        $selectKeywordsDict = array(
            '`FROM`',
            '`WHERE`',
            '`GROUP BY` (ve spojení s voláním funkce `COUNT(*)`)',
            '`ORDER BY`',
            '`LIMIT`',
            '`AND`',
            '`OR`',
            '`IN`',
            '`ASC`',
            '`DESC`'
        );
        $selectKeywords = new SimpleQuestionset('apply');
        $selectKeywords->addRandomizedQuestion('Popište vlastními slovy význam klíčového slova *%keyword%* v kontextu dotazu na databázi (v příkazu `SELECT`). Uveďte příklad příkazu, který toto klíčové slovo používá (můžete si jej vymyslet nebo dohledat) a popište, na co se daný příklad ptá.',
            array('keyword' => $selectKeywordsDict), 2);    
            
        $selectOpening = 'Ve [zkušební aplikaci](http://jan-martinek.com/tmp/db/?sqlite=&username=&db=factbook.db&sql=) vytvořte příkaz, který vypíše ';    
        $selectClosing = '. Příkaz(y) zkopírujte do vstupního pole a okomentujte.';
            
        $select = new SimpleQuestionset('apply', array(
            $selectOpening . 'pouze názvy všech států' . $selectClosing,
            $selectOpening . 'název státu a velikost území na jednoho obyvatele' . $selectClosing,
            $selectOpening . 'všechno, co tabulka s informacemi o státech obsahuje' . $selectClosing,
            $selectOpening . 'všechy státy seřazené podle rychosti populačního růstu' . $selectClosing,
            $selectOpening . 'všechy státy seřazené podle velikosti území (seřazené od nejvyšší hodnoty sestupně)' . $selectClosing,
            $selectOpening . 'velikost území Japonska. Následně sestavte druhý dotaz, který vypíše názvy všech států, které mají větší velikost území než Japonsko' . $selectClosing,
            $selectOpening . 'počet států, jejichž zkratka končí na "a"' . $selectClosing,
            $selectOpening . 'počet států, jejichž zkratka začíná na "a"' . $selectClosing,
        ));                
        
        $modelDict = array(
            'rum',
            'kufr',
            'popelnici',
            'balíček',
            'dráhu',
            'tuleně',
            'obočí',
            'školu',
            'pírko',
            'lízátko',
            'klokana',
            'hrobníka',
            'víno',
            'církev',
            'úrodu',
            'seriál',
            'přehrávač',
            'konopí',
            'váhu',
            'králíka',
            'brk',
            'diář',
            'chemii',
            'benzín',
            'podlahu',
            'lokomotivu',
            'strach',
            'střep',
            'tlak',
            'škodu',
            'obchod',
            'pouto',
            'reproduktor',
            'rostlinu',
            'bombu',
            'vlas',
            'evakuaci',
            'lakomce',
            'šrám',
            'podchod',
            'uhel',
            'rovinu',
            'ukazovátko',
            'hyperbolu',
            'zdravotnictví',
            'veličinu',
            'kryt',
            'minutu',
            'supernovu',
            'nadávku',
            'motor',
            'zídku',
            'křídu',
            'kartel',
            'hlavu',
            'polibek',
            'jazyk',
            'borůvku',
            'šachy',
            'film',
            'zajíce',
            'kru',
            'operetu',
            'utěrku',
            'domov',
            'akt',
            'moment',
            'notifikaci',
            'bankéře',
            'náramek',
            'puchýř',
            'otevřenost',
            'nemčinu',
            'mikinu',
            'eufórii',
            'srandu',
            'beton',
            'bezmoc',
            'avatara',
            'ostrov',
            'pouť',
            'kuličku',
            'opasek',
            'lov',
            'hřib',
            'propisku',
            'telepatii',
            'zub',
            'fotoaparát',
            'samopal',
            'parabolu',
            'rotoped',
            'plenu',
            'štědrost',
            'silnici',
            'Vánoce',
            'královnu',
            'vzpomínku',
            'papír',
            'přítele',
            'skok',
            'mámu',
            'platinu',
            'sníh',
            'Velikonoce',
            'kondom',
            'léčebnu',
            'postel',
            'bezdomovce',
            'frakturu',
            'doutník',
            'bábovku',
            'manažera',
            'revoluci',
            'jelena',
            'šlapadlo',
            'porodnici',
            'věštce',
            'kužel',
            'mouku',
            'rýmu',
            'stůl',
            'zlato',
            'týden',
            'bakaláře',
            'motýla',
            'sysla',
            'dostih',
            'vypalovačku',
            'posla',
            'okurku',
            'bublaninu',
            'stavbu',
            'lesbu',
            'poštu',
            'zápach',
            'zedníka',
            'rovnoběžku',
            'len',
            'košili',
            'kaktus',
            'manželku',
            'revizora',
            'lucernu',
            'koalu',
            'lopuch',
            'internet',
            'pekáč',
            'osu',
            'člověka',
            'prs',
            'alkohol',
            'kulturistu',
            'metodiku',
            'lupu',
            'jed',
            'četbu',
            'vlčici',
            'poupě',
            'krb',
            'sušenku',
            'šišku',
            'klienta',
            'matiku',
            'ocas',
            'řezníka',
            'kolébku',
            'kastrol',
            'drát',
            'radiátor',
            'žurnalistiku',
            'šátek',
            'klan',
            'hypermarket',
            'klání',
            'výhru',
            'prášek',
            'letadlo',
            'virus',
            'motorku',
            'rým',
            'říši',
            'kapra',
            'palici',
            'svátek'
        );
        $model = new SimpleQuestionset('create');
        $model->addRandomizedQuestion(
            'V tomto úkolu budete vytváře zjednodušené ER (Entity-Relationship) diagramy. Inspirujte se dvěma příklady — [nákresem [příkladu s akciemi](http://jan-martinek.com/tmp/db/akcie.png) z předchozí výuky a [tímto nákresem prasete](https://www.gliffy.com/go/publish/9413765). V této lekci je pro nás podstatné zakreslování *entit* (rámečky), u *vztahů* mezi nimi (linky) si zatím vystačíme s jednoduchým propojením bez dalších upřesnění (to až příště).'
            . "\n\n"
            . '> Pokud budete chtít, můžete se podívat na nějaký úvodní článek, který vám pomůže do problematiky lépe proniknout. Googlujte třeba [er diagram databáze úvod](http://www.google.cz/search?q=er+diagramy+%C3%BAvod) (pěkné shrnutí v češtině je [první stránka těchto skript z FI](http://www.fi.muni.cz/~xnovak8/teaching/PB154/pb154-cesky-02.pdf)) nebo v angličtině [intro to er diagrams database](http://www.google.cz/search?q=intro+to+er+diagrams+database). (Soustřeďte se pouze na pasáže o *entitách* a jejich *atributech* — *vztahy* probereme příště a *role* si necháme na jindy.)'
            . "\n\n"
            . 'Pomocí papíru a tužky nebo webového nástroje [Gliffy](https://www.gliffy.com) (registrace zdarma je uživatelsky nepřívětivá, ale zvládnete ji) **vytvořte schéma**, které popisuje **%object%** pomocí alespoň pěti různých tříd entit. (Výše uvedené příklady používají dvě třídy (příklad s akciemi), resp. tři třídy (příklad s prasetem).)'
            . "\n\n"
            . 'Uvědomujte si, že jde o *popis*, který je selektivní — nikdy nepopíšete vše — takže to, jak sestavíte třídy entit závisí na účelu, který váš *ER model* plní. Účel si předem vymyslete a popište do vstupního pole. (U prasete by účelem mohlo být například *ukládání záznamů o prohlídce zvěrolékařem* nebo *modelování reakcí nohou prasete na vizuální podněty v počítačové hře*.)'
            . "\n\n"
            . 'Výsledek na papíru vyfoťte a nahrejte do přílohy, výsledek na webu publikujte veřejně a odkaz zkopírujte do odpovědi.',
            array('object' => $modelDict), 1);


        $remember = new SimpleQuestionset('remember', array(
            'Jaký problém řeší relační databáze?',
            'Co znamená zkratka *SQL* a k čemu se SQL používá?',
            'V jakém software je možné používat SQL (tzn. přímo zadávat příkazy)?',
            'Kdo a kdy navrhl jazyk SQL?',
            'Stručně (alespoň 10 vět) popište historii jazyka SQL.',
            'Zkratka *SQL* má dvě různé formy výslovnosti (*ɛs kjuː ˈɛl* a *siːkwəl*). Zjistěte proč.',
        )); 
        

        $apply = new SimpleQuestionset('apply', array(
            'Uveďte 5 příkladů software (konkrétní produkt), který využívá relační databáze, ale uživatelé v nich sami nezadávají příkazy.',
            'V kurzu se budeme soustředit pouze na *relační* databáze. Popište vlastními slovy, co v tomto kontextu znamená slovo „relace“ a pojem „relační databáze“.',
            'V kurzu se budeme soustředit pouze o relační databáze. Vyjmenujte a krátce vlastními slovy popište, jaké jiné typy databází existují a k čemu se používají.',
            'Rozdělte software, v nichž uživatelé používají příkazy SQL, na několik skupin podle nějaké významné vlastnosti a vlastními slovy popište rozdíly mezi těmito skupinami.',
            'Jaké dialekty jazyka SQL existují? Jsou rozdíly mezi dialekty podstatné pro začátečníka, který s SQL teprve začíná? Proč ano/ne?',
        ));        


        $questions = array_merge(
            $play->getQuestions(1),
            $terms->getQuestions(2),
            $selectKeywords->getQuestions(2),
            $select->getQuestions(2),
            $model->getQuestions(1),
            $remember->getQuestions(2),
            $apply->getQuestions(1)
        );

        return $questions;
    }
    
    public function getRubrics() 
    {
        return array(
            ''
        );
    }

}
