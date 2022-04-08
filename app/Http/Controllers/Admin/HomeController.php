<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\JsonLdMulti;
use Artesaos\SEOTools\Facades\SEOTools;
class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }
    public function searchseo()
    {
        SEOMeta::setTitle('Websolutionstuff | Home');
        SEOMeta::setDescription('This is my page description of websolutionstuff');
        SEOMeta::setCanonical('https://websolutionstuff.com');
        OpenGraph::setDescription('This is my page description of websolutionstuff');
        OpenGraph::setTitle('Websolutionstuff | Home');
        OpenGraph::setUrl('https://websolutionstuff.com');
        OpenGraph::addProperty('type', 'articles');
        TwitterCard::setTitle('Websolutionstuff | Homepage');
        TwitterCard::setSite('@websolutionstuff');
        JsonLd::setTitle('Websolutionstuff | Homepage');
        JsonLd::setDescription('This is my page description of websolutionstuff');
        JsonLd::addImage('https://websolutionstuff.com/frontTheme/assets/images/logo.png');
        // OR use single only SEOTools
        SEOTools::setTitle('Websolutionstuff | Home');
        SEOTools::setDescription('This is my page description of websolutionstuff');
        SEOTools::opengraph()->setUrl('https://websolutionstuff.com/');
        SEOTools::setCanonical('https://websolutionstuff.com');
        SEOTools::opengraph()->addProperty('type', 'articles');
        SEOTools::twitter()->setSite('@websolutionstuff');
        SEOTools::jsonLd()->addImage('https://websolutionstuff.com/frontTheme/assets/images/logo.png');
        return view('post');
    }   

}
