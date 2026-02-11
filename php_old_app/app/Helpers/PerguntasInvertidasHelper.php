<?php

namespace App\Helpers;

class PerguntasInvertidasHelper
{
    /**
     * Lista de textos de preguntas que requieren inversión
     * Identificación por texto para evitar problemas con IDs
     */
    public static function getTextosPerguntasInvertidas(): array
    {
        return [
            'Consigo facilmente entender como os receptores de meus serviços se sentem sobre as coisas.',
            'Consigo lidar de forma eficiente com os problemas dos receptores de meus serviços.',
            'Sinto que influencio de forma positiva as vidas das pessoas através de meu trabalho.',
            'Sinto-me cheio(a) de energia.',
            'Crio um ambiente acolhedor e tranquilo para as pessoas que atendo.',
            'Ganho ânimo e motivação ao interagir diretamente com as pessoas que se beneficiam do meu trabalho.',
            'Consegui fazer várias coisas importantes neste trabalho.',
            'Em meu trabalho, lido com problemas emocionais de forma muito calma.',
            'Tenho clareza sobre minhas funções e responsabilidades.',
            'Sinto que sou ouvido(a) e respeitado(a) no ambiente de trabalho.',
            'Tenho apoio suficiente da liderança ou colegas quando enfrento dificuldades.',
            'As metas e prazos estabelecidos são realistas.',
            'Consigo manter equilíbrio entre vida pessoal e profissional.',
            'Tenho orgulho do que realizo profissionalmente.',
            'Sinto que meu trabalho é importante e significativo.',
            'Consigo resolver de forma eficiente os problemas que surgem no meu trabalho.',
            'Sinto que contribuo bastante com minha organização através do meu trabalho.',
            'Na minha opinião, sou bom(a) no meu trabalho.',
            'Sinto-me entusiasmado(a) quando realizo algo significativo no trabalho.',
            'Consigo fazer várias coisas importantes neste trabalho.',
            'Em meu trabalho, sinto-me confiante sobre minha eficiência ao fazer as coisas.',
        ];
    }

    /**
     * Verifica si una pregunta requiere inversión comparando su texto
     */
    public static function precisaInversao($pergunta): bool
    {
        if (!$pergunta) {
            return false;
        }

        $textoPergunta = trim($pergunta->pergunta ?? '');
        if (empty($textoPergunta)) {
            return false;
        }

        $textosInvertidas = self::getTextosPerguntasInvertidas();

        foreach ($textosInvertidas as $textoInvertido) {
            // Comparación flexible: buscar si el texto contiene la frase clave
            if (stripos($textoPergunta, $textoInvertido) !== false || 
                stripos($textoInvertido, $textoPergunta) !== false) {
                return true;
            }
        }

        return false;
    }
}

