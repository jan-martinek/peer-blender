<?php

namespace Model\Generator\Vikba05;

use Nette;
use Model\Generator\IGenerator;
use Model\Generator\SimpleQuestionset;

class Db2Generator extends Nette\Object implements IGenerator
{
    
    
    public function getPreface() 
    {
        return 'Důraz je tentokrát kladen na kvalitu zpracování kreativních úkolů. V případě, že se zaseknete na neznalosti technikálií a ani Google nepomáhá, neváhejte se obrátit na kolegy v [diskusním fóru předmětu](https://is.muni.cz/auth/diskuse/diskusni_forum_predmet?guz=58445984).';
    }

    public function getQuestions() 
    {
        $termsDict = array(
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
        $terms = new SimpleQuestionset('remember');
        $terms->addRandomizedQuestion('Popište **vlastními slovy** %term% v kontextu relačních databází. Ilustrujte popis alespoň třemi praktickými příklady a odkažte se při vysvětlení na zdroj, z něhož jste čerpali.',
            array('term' => $termsDict), 2); 
        
        
        $playDict = array(
            "SELECT agent.id, agent.name, agent.phone, country.name  \nFROM agent  \nLEFT JOIN country ON country.id = agent.country_id  \nWHERE area <= 400000 AND population > 100000000",
            "SELECT agent.id, agent.name, agent.phone, country.name  \nFROM agent  \nLEFT JOIN country ON country.id = agent.country_id  \nWHERE area <= 400000 AND population < 100000000",
            "SELECT agent.id, agent.name, agent.phone, country.name, population_growth  \nFROM agent  \nLEFT JOIN country ON country.id = agent.country_id  \nWHERE population_growth < 1",
            "SELECT agent.id, agent.name, agent.phone, country.name, population_growth  \nFROM agent  \nLEFT JOIN country ON country.id = agent.country_id  \nWHERE population_growth > 2",
            "SELECT agent.id, agent.name, agent.email, country.name  \nFROM agent  \nLEFT JOIN country ON country.id = agent.country_id  \nWHERE area <= 400000 AND population > 100000000",
            "SELECT agent.id, agent.name, agent.email, country.name  \nFROM agent  \nLEFT JOIN country ON country.id = agent.country_id  \nWHERE area <= 400000 AND population < 100000000",
            "SELECT agent.id, agent.name, agent.email, country.name, population_growth  \nFROM agent  \nLEFT JOIN country ON country.id = agent.country_id  \nWHERE population_growth < 1",
            "SELECT agent.id, agent.name, agent.email, country.name, population_growth  \nFROM agent  \nLEFT JOIN country ON country.id = agent.country_id  \nWHERE population_growth > 2",
        );        
        $play = new SimpleQuestionset('apply');
        $play->addRandomizedQuestion("Bez toho, že byste si příliš pročítali materiály, si otevřete [zkušební aplikaci](http://jan-martinek.com/tmp/db/?sqlite=&username=&db=factbook.db&sql=) a vložte do pole pro SQL příkaz tento příkaz:\n\n`%select%`\n\n- Popište, co obsahuje výsledná tabulka (pokud se objeví chyba, nejspíše jste příkaz zkopírovali špatně — nevíte-li si rady, optejte se na fóru).\n- Zkuste měnit parametry v dotazu a popište, jak se výsledky mění, když pozměněný příkaz spustíte (pomocí tlačítka *Execute*).\n- Zkuste o agentech najít o agentech další informace a vysvětlete *podivné hodnoty*, které se týkají města, kde agent pobývá.",
            array('select' => $playDict), 1);
        
        $play2 = new SimpleQuestionset('apply', array(
            'Ve [stejné aplikaci](http://jan-martinek.com/tmp/db/?sqlite=&username=&db=factbook.db) jako v předchozím příkladu si nejprve pečlivě prohlédněte dostupné informace v tabulkách (v menu vlevo můžete vždy kliknout buď na příkaz „select“ nebo na název tabulky, vyzkoušejte si to).'
            . "\n\n"
            . 'Poté vytvořte 3 různé příkazy, které spojují data z obou tabulek a zjišťují něco konkrétního. Alespoň v jednom z příkladů použijte některou [agregační funkci](http://google.com/search?q=databáze+agregační+funkce). Příkazy vložte do vstupního pole a popište jejich účel.'
        ));
                     
        
        $modelDict = array(
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
        $model = new SimpleQuestionset('create');
        $model->addRandomizedQuestion(
            'V tomto úkolu budete opět vytvářet zjednodušené ER (Entity-Relationship) diagramy, přetvoříte je do tabulky a následně zrevidujete, zda je Váš diagram správný.'
            . "\n\n"
            . 'Pomocí papíru a tužky nebo webového nástroje [Gliffy](https://www.gliffy.com) **vytvořte schéma**, které popisuje objekt **„%object%“** pomocí alespoň pěti různých entit, každou s alespoň třemi atributy. Vyznačte vazby mezi entitami včetně jejich [kardinality](https://www.google.cz/search?q=kardinalita+datab%C3%A1ze). Stejně jako minule si uvědomujte, že jde o *popis*, který je selektivní — nikdy nepopíšete vše — takže to, jak sestavíte třídy entit závisí na účelu, který váš *ER model* plní. **Účel si předem vymyslete a popište do vstupního pole.**'
            . "\n\n"
            . "**Druhá část úkolu je vytvoření samotných tabulek** pomocí nástroje [*Adminer*](http://jan-martinek.com/tmp/db/?sqlite=) a databáze *SQLite*. Oba již trochu znáte z předchozích příkladů. Díky Admineru nebudete muset v tomto úkolu používat přímo SQL příkazů."
            . "\n\n"
            . "Na [přihlašovací stránce](http://jan-martinek.com/tmp/db/?sqlite=) se připojte do své vlastní databáze, která má název ve tvaru `UČO.db` (tedy např. `123456.db`). Poté pomocí odkazu `Create table` vytvořte tabulku pro každou z vašich entit — název entity bude názvem tabulky a každý atribut vytvoří sloupec v tabulce, pozor dejte na správný výběr datových typů. Nezapomeňte na identifikátory a propojení mezi tabulkami pomocí cizích klíčů (když sloupec nazvete jako *existující tabulku* s koncovkou \"\_id\" (tedy např. \"akcie\_id\", Adminer vám napoví)."
            . "\n\n"
            . "**Poté, co tabulky vytvoříte, zbývá poslední krok: plnění.** Do každé tabulek vyplňte alespoň 5 vzorových *smysluplných* řádků (pomocí odkazu `New item`). Jak takové tabulky mohou vypadat znáte z původního příkladu se zeměmi a agenty. Po naplnění tabulek zkontrolujte, zda vaše schéma odpovídá výsledné databázi a vše odevzdejte do přílohy: "
            . "\n\n"
            . 'Výsledné schéma na papíru vyfoťte a nahrejte do přílohy nebo publikujte veřejně webovou verzi a odkaz zkopírujte do odpovědi. Výslednou databázi vyexportujte (odkaz `Export`, vyberte Output "gzip" a Format "SQL"). Vše zazipujte dohromady i s úkolem z následující úlohy a nahrajte do odevzdávárny.',
            array('object' => $modelDict), 1);


        $normalizeDictPrep = array(
            'občan' => array(
                '"id" int',
                '"jméno" text',
                '"příjmení" text',
                '"číslo_OP" int',
                '"obec" text',
                '"ulice" text',
                '"číslo_popisné" int',
                '"PSČ" text',
                '"zaměstnavatel" text',
                '"adresa_zaměstnavatele_obec" text',
                '"adresa_zaměstnavatele_ulice" text',
                '"adresa_zaměstnavatele_číslo_popisné" int',
                '"adresa_zaměstnavatele_PSČ" text'
            ),
            'kniha' => array(
                '"id" int',
                '"název" text',
                '"rok_vydání" int',
                '"rok_prvního_vydání" int',
                '"ISBN" text',
                '"skladem" bool',
                '"autor" text',
                '"autor_je_naživu" bool',
                '"vydavatel" text',
                '"adresa_vydavatele_obec" text',
                '"adresa_vydavatele_ulice" text',
                '"adresa_vydavatele_číslo_popisné" int',
                '"adresa_vydavatele_PSČ" text',
                '"dodavatel" text',
                '"adresa_dodavatele_obec" text',
                '"adresa_dodavatele_ulice" text',
                '"adresa_dodavatele_číslo_popisné" int',
                '"adresa_dodavatele_PSČ" text'
            ),
            'kniha' => array(
                '"id" int',
                '"název" text',
                '"rok_vydání" int',
                '"rok_prvního_vydání" int',
                '"ISBN" text',
                '"skladem" bool',
                '"autor" text',
                '"rok_narození_autora" int',
                '"rok_úmrtí_autora" int',
                '"autor_je_naživu" text',
                '"vydavatel" text',
                '"adresa_vydavatele_obec" text',
                '"adresa_vydavatele_ulice" text',
                '"adresa_vydavatele_číslo_popisné" int',
                '"adresa_vydavatele_PSČ" text',
                '"dodavatel" text',
                '"adresa_dodavatele_obec" text',
                '"adresa_dodavatele_ulice" text',
                '"adresa_dodavatele_číslo_popisné" int',
                '"adresa_dodavatele_PSČ" text'
            ),
            'kniha' => array(
                '"id" int',
                '"název" text',
                '"rok_vydání" int',
                '"rok_prvního_vydání" int',
                '"ISBN" text',
                '"půjčená" text',
                '"autor" text',
                '"rok_narození_autora" int',
                '"rok_úmrtí_autora" int',
                '"autor_je_naživu" text',
                '"poslední_výpůjčka_datum" text',
                '"poslední_výpůjčka_uživatel_jméno" text',
                '"poslední_výpůjčka_uživatel_vráceno_dne" date',
                '"poslední_výpůjčka_uživatel_půjčeno_dne" date',
                '"dodavatel" text',
                '"adresa_dodavatele_obec" text',
                '"adresa_dodavatele_ulice" text',
                '"adresa_dodavatele_číslo_popisné" int',
                '"adresa_dodavatele_PSČ" text'
            ),
            'kniha' => array(
                '"id" int',
                '"název" text',
                '"rok_vydání" int',
                '"rok_prvního_vydání" int',
                '"ISBN" text',
                '"skladem" bool',
                '"autor" text',
                '"rok_narození_autora" int',
                '"rok_úmrtí_autora" int',
                '"autor_je_naživu" text',
                '"dodavatel" text',
                '"adresa_dodavatele_obec" text',
                '"adresa_dodavatele_ulice" text',
                '"adresa_dodavatele_číslo_popisné" int',
                '"adresa_dodavatele_PSČ" text'
            ),
            'osoba' => array(
                '"id" int',
                '"jméno" text',
                '"příjmení" text',
                '"číslo_OP" int',
                '"obec" text',
                '"ulice" text',
                '"číslo_popisné" int',
                '"PSČ" text',
                '"lékař" text',
                '"adresa_lékaře_obec" text',
                '"adresa_lékaře_obec" text',
                '"adresa_lékaře_ulice" text',
                '"adresa_lékaře_číslo_popisné" int',
                '"adresa_lékaře_PSČ" text'
            ),
            'osoba' => array(
                '"id" int',
                '"jméno" text',
                '"příjmení" text',
                '"číslo_OP" int',
                '"obec" text',
                '"ulice" text',
                '"číslo_popisné" int',
                '"PSČ" text',
                '"pojišťovna" text',
                '"lékař" text',
                '"číslo_pojištěnce" int',
                '"adresa_pojišťovny_obec" text',
                '"adresa_pojišťovny_obec" text',
                '"adresa_pojišťovny_ulice" text',
                '"adresa_pojišťovny_číslo_popisné" int',
                '"adresa_pojišťovny_PSČ" text'
            ),
            'literární_směr' => array(
                '"id" int',
                '"název" text',
                '"zásadní_od" int',
                '"zásadní_do" int',
                '"stylotvorné" text prvky',
                '"autor_1_jméno" text',
                '"autor_1_země" text',
                '"autor_1_rok_narození" text',
                '"autor_2_jméno" text',
                '"autor_2_země" text',
                '"autor_2_rok_narození" text',
                '"autor_3_jméno" text',
                '"autor_3_země" text',
                '"autor_3_rok_narození" text',
                '"autor_4_jméno" text',
                '"autor_4_země" text',
                '"autor_4_rok_narození" text',
                '"autor_5_jméno" text',
                '"autor_5_země" text',
                '"autor_5_rok_narození" text',
                '"dílo_1_název" text',
                '"dílo_1_autor" text',
                '"dílo_1_rok_vydání" text',
                '"dílo_2_název" text',
                '"dílo_2_autor" text',
                '"dílo_2_rok_vydání" text',
                '"dílo_3_název" text',
                '"dílo_3_autor" text',
                '"dílo_3_rok_vydání" text',
                '"dílo_4_název" text',
                '"dílo_4_autor" text',
                '"dílo_4_rok_vydání" text',
                '"dílo_5_název" text',
                '"dílo_5_autor" text',
                '"dílo_5_rok_vydání" text'
            ),
            'literární_směr' => array(
                '"id" int',
                '"název" text',
                '"zásadní_od" int',
                '"zásadní_do" int',
                '"stylotvorné" text prvky',
                '"autor_1_jméno" text',
                '"autor_1_země" text',
                '"autor_1_rok_narození" text',
                '"autor_2_jméno" text',
                '"autor_2_země" text',
                '"autor_2_rok_narození" text',
                '"autor_3_jméno" text',
                '"autor_3_země" text',
                '"autor_3_rok_narození" text',
                '"autor_4_jméno" text',
                '"autor_4_země" text',
                '"autor_4_rok_narození" text',
                '"autor_5_jméno" text',
                '"autor_5_země" text',
                '"autor_5_rok_narození" text',
                '"kniha_1_název" text',
                '"kniha_1_autor" text',
                '"kniha_1_rok_vydání" text',
                '"kniha_2_název" text',
                '"kniha_2_autor" text',
                '"kniha_2_rok_vydání" text',
                '"kniha_3_název" text',
                '"kniha_3_autor" text',
                '"kniha_3_rok_vydání" text',
                '"kniha_4_název" text',
                '"kniha_4_autor" text',
                '"kniha_4_rok_vydání" text',
                '"kniha_5_název" text',
                '"kniha_5_autor" text',
                '"kniha_5_rok_vydání" text',
                '"povídka_1_název" text',
                '"povídka_1_autor" text',
                '"povídka_1_rok_vydání" text',
                '"povídka_2_název" text',
                '"povídka_2_autor" text',
                '"povídka_2_rok_vydání" text',
                '"povídka_3_název" text',
                '"povídka_3_autor" text',
                '"povídka_3_rok_vydání" text',
                '"povídka_4_název" text',
                '"povídka_4_autor" text',
                '"povídka_4_rok_vydání" text',
                '"povídka_5_název" text',
                '"povídka_5_autor" text',
                '"povídka_5_rok_vydání" text'
            ),
            'výtvarný_směr' => array(
                '"id" int',
                '"název" text',
                '"zásadní_od" int',
                '"zásadní_do" int',
                '"stylotvorné" text prvky',
                '"zástupce_1_jméno" text',
                '"zástupce_1_země" text',
                '"zástupce_1_rok_narození" text',
                '"zástupce_2_jméno" text',
                '"zástupce_2_země" text',
                '"zástupce_2_rok_narození" text',
                '"zástupce_3_jméno" text',
                '"zástupce_3_země" text',
                '"zástupce_3_rok_narození" text',
                '"zástupce_4_jméno" text',
                '"zástupce_4_země" text',
                '"zástupce_4_rok_narození" text',
                '"zástupce_5_jméno" text',
                '"zástupce_5_země" text',
                '"zástupce_5_rok_narození" text',
                '"dílo_1_název" text',
                '"dílo_1_autor" text',
                '"dílo_1_rok_vydání" text',
                '"dílo_2_název" text',
                '"dílo_2_autor" text',
                '"dílo_2_rok_vydání" text',
                '"dílo_3_název" text',
                '"dílo_3_autor" text',
                '"dílo_3_rok_vydání" text',
                '"dílo_4_název" text',
                '"dílo_4_autor" text',
                '"dílo_4_rok_vydání" text',
                '"dílo_5_název" text',
                '"dílo_5_autor" text',
                '"dílo_5_rok_vydání" text'
            )
        );

        $normalizeDict = array();
        foreach ($normalizeDictPrep as $table => $attributes) {
            $normalizeDict[] = "jedinou tabulkou „" . $table . "“, která obsahuje sloupce\n\n- " . str_replace('_', '\_', implode("\n- ", $attributes));
        }

        $normalize = new SimpleQuestionset('create');
        $normalize->addRandomizedQuestion(
            '**V tomto úkolu budete normalizovat databázi do třetí normální formy.** Prakticky jde o pragmatické roztřídění různých atributů do entit tak, aby data nebyla redundantní (aby nezabírala příliš mnoho místa), aby se s nimi dalo dobře pracovat (vše není v jedné tabulce, ale vzájemně závislé atributy jsou společně) a aby nedocházelo k nekonzistenci dat. Pokud bychom například měli u každého zaměstnance uvedenou adresu s městem a PSČ, může se stát, že stejná obec bude mít různá PSČ — tomu se můžeme vyhnout tím, že vytvoříme samostatnou tabulku, kde ke každému PSČ uvedeme město a názvy měst z tabulky zaměstnanců úplně odstraníme.'
            . "\n\n"
            . '> Více informací najdete zejména [na české Wikipedii](https://www.google.cz/search?q=datab%C3%A1ze+normalizace+wiki). Byť Wikipedie není určena ke sdílení návodů, právě návod na stránce [Třetí normální forma](https://cs.wikipedia.org/wiki/Třet%C3%AD_normáln%C3%AD_forma) ilustruje principy normalizace poměrně hezky.'
            . "\n\n"
            . "**Druhá část úkolu je vytvoření samotných tabulek** pomocí nástroje [*Adminer*](http://jan-martinek.com/tmp/db/?sqlite=), obdobně jako v předchozím úkolu. Vytvořte tabulky (alespoň tři), nezapomeňte na cizí klíče, vložte do každé tabulky alespoň tři vzorové smysluplné řádky. Schéma i obsah databáze odevzdejte stejným způsobem jako v předchozím úkolu. Do vstupního pole popište, co taková databáze nejspíše popisuje."
            . "\n\n"
            . "> Databázi si před začátkem práce na novém úkolu pročistěte. Tabulky smažete pomocí tlačítka `Drop`. Dejte pozor, abyste si předtím vyexportovali výsledky předchozího úkolu. Pokud se budete chtít k původnímu úkolu vrátit, můžete vyexportovaný soubor importovat zpět."
            . "\n\n"
            . "**A teď samotný úkol:** Normalizujte databázi s %table%",
            array('table' => $normalizeDict), 1);

        $join = new SimpleQuestionset('apply', array(
            "Využijte libovolnou databázi, se kterou jste pracovali v tomto úkolu (využití vlastní databáze si cením více a bude zdůrazněno v peer-assessmentu) a **vytvořte několik dotazů (alespoň 3), které spojují více tabulek**.\n\nTyto dotazy vlastními slovy popište: co vybírají a k čemu by takový výsledek bylo možné využít (např. seznam agentů v zemích, kde je vysoký populační růst, by mohl být použitelný jako seznam kontaktů pro sociologický výzkum)."
        ));

        $questions = array_merge(
            $terms->getQuestions(2),
            $play->getQuestions(1),
            $play2->getQuestions(1),
            $model->getQuestions(1),
            $normalize->getQuestions(1),
            $join->getQuestions(1)
        );

        return $questions;
    }
    
    public function getRubrics() 
    {
        return array();
    }

}
