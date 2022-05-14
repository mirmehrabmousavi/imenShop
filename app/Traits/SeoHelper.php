<?php


namespace App\Traits;


use App\Models\Setting;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\SEOTools;

trait SeoHelper
{

    public function setIndexSeo(){
        $title = Setting::where('key' , 'title')->pluck('value')->first() ?:'' ;
        $shortActivity = Setting::where('key' , 'descriptionSeo')->pluck('value')->first() ?:'' ;
        $keyword = Setting::where('key' , 'keywords')->pluck('value')->first() ?: [] ;
        if ($keyword){
            $keywordFinal = explode(',' , $keyword) ;
        }else{
            $keywordFinal = [];
        }
        $urlSite = Setting::where('key' , 'address')->pluck('value')->first() ?:'' ;
        $logoSite = Setting::where('key' , 'logo')->pluck('value')->first() ?:'' ;
        SEOTools::setTitle($title);
        SEOTools::setDescription($shortActivity);
        SEOTools::opengraph()->setUrl($urlSite);
        SEOTools::setCanonical($urlSite);
        SEOMeta::addKeyword($keywordFinal);
        SEOTools::opengraph()->addProperty('type', 'store');
        //twitter
        SEOTools::twitter()->setSite($title);
        SEOTools::twitter()->setDescription($shortActivity);
        SEOTools::twitter()->setTitle($title);
        SEOTools::twitter()->setImage($urlSite . $logoSite);
        SEOTools::twitter()->addValue('image:alt' , $title);
        SEOTools::twitter()->addValue('card' , 'summary');
        SEOTools::jsonLd()->addImage($urlSite . $logoSite);
        SEOTools::jsonLd()->setTitle($title);
        SEOTools::jsonLd()->setDescription($shortActivity);
        SEOTools::jsonLd()->setType('WebSite');
        SEOTools::jsonLd()->addValues([
            'url' => $urlSite,
            'potentialAction' => [
                '@type' => "SearchAction",
                'target' => $title."archive/search?search={search}",
                'query-input' => "required name=search"
            ]
        ]);
        //OpenGraph
        OpenGraph::addProperty('locale', 'fa');
        OpenGraph::setSiteName($title);
        OpenGraph::addImage($urlSite . $logoSite);
        OpenGraph::setTitle($title); // define title
        OpenGraph::setDescription($shortActivity);  // define description
        OpenGraph::setUrl($urlSite); // define url
    }

    public function seoSingleSeo($title , $description , $type , $url , $image){
        $titleSite = Setting::where('key' , 'title')->pluck('value')->first() ?:'' ;
        $urlSite = Setting::where('key' , 'address')->pluck('value')->first() ?:'' ;
        $logoSite = Setting::where('key' , 'logo')->pluck('value')->first() ?:'' ;
        SEOTools::setTitle($title);
        SEOTools::opengraph()->setUrl($urlSite .  $url);
        SEOTools::setCanonical($urlSite .  $url);
        SEOTools::setDescription($description);
        SEOTools::opengraph()->addProperty('type', 'WebSite');
        //twitter
        SEOTools::twitter()->setSite($titleSite);
        SEOTools::twitter()->setDescription($description);
        SEOTools::twitter()->setTitle($title);
        SEOTools::jsonLd()->addImage($urlSite . $image);
        SEOTools::jsonLd()->setTitle($title);
        SEOTools::jsonLd()->setDescription($description);
        SEOTools::jsonLd()->setType('WebSite');
        SEOTools::twitter()->setImage($urlSite . $image);
        //OpenGraph
        OpenGraph::addProperty('locale', 'fa');
        OpenGraph::setSiteName($titleSite);
        OpenGraph::addImage($urlSite . $image);
        OpenGraph::setTitle($title); // define title
        OpenGraph::setDescription($description);  // define description
        OpenGraph::setUrl($urlSite .  $url); // define url
    }

}
