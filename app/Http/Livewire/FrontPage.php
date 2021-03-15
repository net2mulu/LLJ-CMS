<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Page;

class FrontPage extends Component
{
    public $title;
    public $slug;
    public $content;
    public $urlSlug;
    
    /**
     * mount
     *
     * @param  mixed $urlSlug
     * @return void
     */
    public function mount($urlSlug = null)
    {
        $this->retriveContent($urlSlug);
    }
    
    public function retriveContent($urlSlug)
    {
        //get home is slug is empty
        if(empty($urlSlug)){
            $data = Page::where('is_default_home' , true)->first();
        }else {
            // get according to th epage
            $data = Page::where('slug' , $urlSlug)->first();
        }
       // if not found
       if(!$data)
       {
        $data = Page::where('is_default_not_found' , true)->first();
       }
        $this->title = $data->title;
        $this->content = $data->content;
    }
    
    /**
     * render
     *
     * @return void
     */
    public function render()
    {
        return view('livewire.front-page')->layout('layouts.front');
    }
}
