<?php

namespace Model\Generator\Vikba05;

use Nette;
use Model\Generator\IGenerator;
use Model\Generator\SimpleQuestionset;

class MarkdownGenerator extends Nette\Object implements IGenerator
{
    
    
    public function getPreface() 
    {
        return 'Při zpracování úkolů vycházejte ze zadané literatury a dohledávejte další informace na internetu.';
    }

    public function getQuestions() 
    {
        
        $remember = new SimpleQuestionset('remember',
            array(
                'Proč byla vymyšlena syntaxe Markdown?',
                'Jakou roli hrají v syntaxi Markdown hranaté závorky?',
                'Jak se v syntaxi Markdown používá hvězdička?',
                'Jak v syntaxi Markdown zapíšete číslovaný seznam?',
                'Jak v syntaxi Markdown zapíšete nečíslovaný seznam?',
                'Jak v syntaxi Markdown zapíšete webový odkaz? (Jaké jsou jeho druhy?)',
                'Jak v syntaxi Markdown zapíšete nadpis?'
            )
        );
        
        $apply = new SimpleQuestionset('apply',
            array(
                'Popište vlastními slovy „filozofii“ syntaxe Markdown.',
                'Co je to „inline html“?',
                'Co je to „escaping“ (česky se říká „escapování“) znaků?',
                'Proč Markdown umožňuje používání odkazů pomocí reference?',
                'Pokud chcete napsat kus programového kódu, na který se nebudou aplikovat pravidla syntaxe Markdown, jak to uděláte?'
            )
        );

        $gruberAndDialects = new SimpleQuestionset('analyze', 
            array('Na syntaxi Markdown spolupracoval Aaron Swartz. Najděte si o něm více informací, a popište v 10 větách projekty, na nichž pracoval. Uveďte odkazy na 3 zdroje, z nichž jste čerpali. (Zde ne Wikipedia.)',
                'Na syntaxi Markdown spolupracoval Aaron Swartz. Najděte si o něm více informací, a popište jeho osobní život v 10 větách. Uveďte odkazy na 3 zdroje, z nichž jste čerpali. (Zde ne Wikipedia.)')
        );
        
        $swartz = new SimpleQuestionset('analyze',
            array('Autorem syntaxe Markdown je John Gruber. Najděte, o koho jde, a popište jej v 10 větách. Uveďte odkazy na 3 zdroje, z nichž jste čerpali. (Zde ne Wikipedia.)',
            'Díky rozšíření syntaxe Markdown vzniklo poměrně velké množství dialektů. Dohledejte alespoň čtyři dialekty Markdown („markdown dialects“), popište jejich užití a uveďte odkazy na alespoň 4 zdroje, z nichž jste čerpali. (Zde ne Wikipedia.)')
        );      
        
        $syntaxes = array('Setext', 'atx', 'Textile', 'reStructuredText', 'Grutatext', 'EtText');
        $analyze = new SimpleQuestionset('analyze');
        $analyze->addRandomizedQuestion('Popište v základech podobnosti a rozdíly syntaxe **%syntax%** vůči Markdownu.', array('syntax' => $syntaxes));  
        
        $create = new SimpleQuestionset('create');
        $create->addRandomizedQuestion(
            'Použijte alespoň 3000 slov z textu knihy dostupné na adrese [%book%](%book%) 
            (vyjděte z verze *Plain Text UTF-8*) a naformátujte ji pomocí syntaxe Markdown.' 
            . "\n\n"
            . 'V textu následně zvýrazněte důležité pasáže vhodným způsobem (pasáže vyberte dle 
            vlastního uvážení). Do vstupního pole vepište jakékoli postřehy a poznámky související s úkolem.'
            . "\n\n"
            . 'Následně soubor se syntaxí Markdown **vložte do [nějakého webového generátoru](https://www.google.cz/search?rls=en&q=markdown+convertor&ie=UTF-8&oe=UTF-8&gfe_rd=cr&ei=kr0XVrLqEcyk8weihYCQBQ) HTML z Markdownu** a 
            oba soubory (výsledné HTML i zdrojový soubor v Markdownu) zazipujte. Soubor ZIP vložte jako přílohu k úkolu.',
            array(
                'book' => array(     
                    'http://www.gutenberg.org/ebooks/133',
                    'http://www.gutenberg.org/ebooks/12',
                    'http://www.gutenberg.org/ebooks/528',
                    'http://www.gutenberg.org/ebooks/1343',
                    'http://www.gutenberg.org/ebooks/5200',
                    'http://www.gutenberg.org/ebooks/46',
                    'http://www.gutenberg.org/ebooks/175',
                    'http://www.gutenberg.org/ebooks/1',
                    'http://www.gutenberg.org/ebooks/2',
                    'http://www.gutenberg.org/ebooks/3'
                )
            )
        );
        
        $questions = array_merge(
            $remember->getQuestions(2),
            $apply->getQuestions(2),
            $gruberAndDialects->getQuestions(1),
            $swartz->getQuestions(1),
            $analyze->getQuestions(1),
            $create->getQuestions(1)
        );
        
        return $questions;
    }
    
    public function getRubrics() 
    {
        return array(
            'Jsou odpovědi vědomostní otázky (tzn. první čtyři) správné? Pokud ne, upřesněte, 
            kde autor/ka řešení dělá chyby. Pokuste se to vysvětlit tak, aby to pomohlo v pochopení.',
            'Dohledal/a autor/ka vhodně informace v otázkách číslo 5 a 6?',
            'Jsou praktické úkoly provedeny správně? Je syntax Markdown užita správně?',
            'Zhodnoťte, zda jsou zvýrazněné pasáže v textu knihy v posledním úkolu vybrané vhodně 
            a zvýrazněné správným způsobem. Pokud byste raději zvýraznili jiné pasáže, jaké by to byly?'
        );
    }

}
