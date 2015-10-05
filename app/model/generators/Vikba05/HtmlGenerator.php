<?php

namespace Model\Generator\Vikba05;

use Nette;
use Model\Generator\IGenerator;
use Model\Generator\SimpleQuestionset;

class HtmlGenerator extends Nette\Object implements IGenerator
{
    
    
    public function getPreface() 
    {
        return 'Při zpracování úkolů vycházejte ze zadané literatury a dohledávejte další informace na internetu.';
    }
    

    public function getQuestions() 
    {
        
        $elements = array('p', 'h1', 'h2', 'h6', 'ol', 'ul', 'li', 'title', 'br', 'head');
        
        $html = new SimpleQuestionset('remember');
        $html->addRandomizedQuestion('Jaká je sémantika prvku `%el%` v HTML dokumentu? 
            Kde se takto vyznačený prvek v dokumentu objevuje?', array('el' => $elements), 3);
        
        $remember = new SimpleQuestionset('remember',
            array(
                'Co znamená zkratka HTML?',
                'Je pravda, že HTML je značkovací jazyk? Co to znamená? K čemu slouží jazyk HTML?',
                'Jak interpretuje prohlížeč mezery, tabulátory, nové řádky a jiné neviditelné znaky (white-space characters) v kódu stránky?',
                'Jaký je rozdíl mezi termíny *HTML značky* a *HTML tagy*?',
            )
        );

        $understand = new SimpleQuestionset('understand',
            array(
                'Co umožňuje užívání HTML? (Popište, co umožňuje jak v rámci jedné stránky, tak mezi stránkami.)',
                'K čemu slouží atributy `src` a `alt` u obrázku?',
                'Popište rozdíl mezi seznamem značeným `<ol> … </ol>` a seznamem uzavřeným ve značkách `<ul> … </ul>`.
                Pro jaké seznamy je vhodný první a druhý typ seznamu?'
            )  
        );
        
        $apply = new SimpleQuestionset('apply',
            array(
                'Vložte libovolným způsobem kód stránky kisk.cz do 
                [validátoru](https://www.google.cz/search?rls=en&q=co+je+to+valid%C3%A1tor) 
                a do odpovědi zkopírujte chyby a varování nalezené validátorem.',
                
                'Najděte na internetu stránku s vysvětlením, jak v HTML psát podtitulky 
                (tzn. doplňující text k titulku, viz příklad níže) dle specifikace HTML5. Do 
                odpovědi vlastními slovy shrňte toto vysvětlení a zkopírujte URL (adresu) nalezené 
                stránky. Dejte si skutečně pozor na to, aby šlo o návod pro *HTML 5*.' 
                . "\n\n"
                . '<big><big><strong>Technology and The Evolution of Storytelling</strong></big></big><br><strong style="line-height: 1.6em;">It is such an exciting time to be a filmmaker.</strong>',
                
                'Vytvořte v HTML číslovaný nákupní seznam. Jako jeho položky použijte jablko, máslo, 
                jogurt a dvě vymyšlené položky dle vlastní fantazie (v libovolném pořadí). Zapište kód do odpovědi.',
                
                'Najděte chybu v následujícím kódu a popište ji.  
                `<strong>Nad všecko věren buď <em>sobě samému.</strong></em>`',

                'Najděte v kódu stránky na adrese [seznam.cz](http://seznam.cz) nečíslovaný seznam a jeho kód zkopírujte do odpovědi.'

                
            )
        );
        
        $create = new SimpleQuestionset('create');
        $create->addRandomizedQuestion(
            'Použijte alespoň 1000 slov z textu knihy dostupné na adrese [%book%](%book%) 
            (vyjděte z verze *Plain Text UTF-8*) a vytvořte z ní validní a relativně smysluplný HTML dokument.' 
            . "\n\n"
            . 'Využijte *alespoň* prvků odstavce a nadpisů a korektně vytvořte hlavičku dokumentu (prvek `head`). 
            V textu následně zvýrazněte důležité pasáže vhodným způsobem (pasáže vyberte dle 
            vlastního uvážení). Do vstupního pole vepište jakékoli postřehy a poznámky související s úkolem. 
            **HTML soubor vložte jako přílohu k úkolu.**',
            array(
                'book' => array(     
                    'http://www.gutenberg.org/ebooks/1342',
                    'http://www.gutenberg.org/ebooks/11',
                    'http://www.gutenberg.org/ebooks/1232',
                    'http://www.gutenberg.org/ebooks/76',
                    'http://www.gutenberg.org/ebooks/5200',
                    'http://www.gutenberg.org/ebooks/84',
                    'http://www.gutenberg.org/ebooks/174',
                    'http://www.gutenberg.org/ebooks/23t'
                )
            )
        );
        
        $questions = array_merge(
            $html->getQuestions(1),
            $remember->getQuestions(2),
            $understand->getQuestions(1),
            $apply->getQuestions(2),
            $create->getQuestions(1)
        );
        
        return $questions;
    }
    
    public function getRubrics() 
    {
        return array(
            'Jsou odpovědi vědomostní otázky (tzn. první čtyři) správné? Pokud ne, upřesněte, 
            kde autor/ka řešení dělá chyby. Pokuste se to vysvětlit tak, aby to pomohlo v pochopení.',
            'Jsou praktické úkoly provedeny správně? Jsou prvky HTML kódu užity správně?',
            'Zhodnoťte, zda jsou zvýrazněné pasáže v textu knihy v posledním úkolu vybrané vhodně 
            a zvýrazněné správným způsobem. Pokud byste raději zvýraznili jiné pasáže, jaké by to byly?'
        );
    }

}
