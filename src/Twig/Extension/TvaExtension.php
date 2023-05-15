<?php
 
namespace App\Twig\Extension;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
class TvaExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('tva', array($this,'calculTva')),
        ];
    }

    public function calculTva($prixHT,$tva)
    {
        return round($prixHT+($prixHT * $tva),2);
    }
    
    public function getName()
    {
        return 'tva_extension';
    }
}