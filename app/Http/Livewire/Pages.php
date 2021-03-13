<?php

namespace App\Http\Livewire;

use Illuminate\Validation\Rule;
use App\Models\Page;
use Livewire\Component;
use Livewire\WithPagination;

class Pages extends Component
{
    use WithPagination;

    public $slug , $title, $content, $modelId ,$confirmingUserDeletion;
    public $modalFormVisible = false;
    public $modalConfirmDeleteVisible = false;
            
    /**
     * validation rules
     *
     * @return void
     */
    public function  rules()
    {
        return [
            'title' => 'required',
            'slug' => ['required', Rule::unique('pages','slug')->ignore($this->modelId)],
            'content' => 'required'
        ];
    }
    
    /**
     * updatedTitle
     *
     * @param  mixed $value
     * @return void
     */
    public function updatedTitle($value)
    {
        $this->generateSlug($value);
    }
    /**
     * create new record in db
     *
     * @return void
     */
    public function create()
    {
        $this->validate();
        $create = Page::create($this->modelData());
        if(!$create){dd($create);}
        $this->modalFormVisible = false;
        $this->resetrVars();
    }       
    /**
     * update
     *
     * @return void
     */
    public function update()
    {
        $this->validate();
        Page::find($this->modelId)->update($this->modelData());
        $this->modalFormVisible = false;
    } 

    public function delete()
    {
        dd('deleting');
        Pag::destroy($this->modelId);
        $this->modalConfirmDeleteVisible = false;
        $this->resetrVars();

    }
    /**
     * updateShowModal
     *
     * @param  mixed $id
     * @return void
     */
    public function updateShowModal($id)
    {
        $this->resetValidation();
        $this->resetrVars();
        $this->modelId = $id;
        $this->modalFormVisible = true;
        $this->loadModel();
    }    
    /**
     * deleteShowModal
     *
     * @return void
     */
    public function deleteShowModal($id)
    {
        $this->modelId = $id;
        $this->modalConfirmDeleteVisible = true;

    }    
    /**
     * read
     *
     * @return void
     */
    public function read()
    {
       return Page::paginate(5);
    }
    /**
     * Shows the form Modal
     * of the create function
     *
     * @return void
     */
    public function createShowModal()
    {
        $this->resetValidation();
        $this->resetrVars();
        $this->modalFormVisible = true;
    }
        
    /**
     * modelData mapped in this component
     *
     * @return void
     */
    public function modelData()
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content
        ];
    }
    
    /**
     * after the insertion this function is called for reseting the values of
     * the variables.
     *
     * @return void
     */
    public function resetrVars() 
    {
        $this->modelId = null;
        $this->title = null;
        $this->slug = null;
        $this->content = null;
    }
    
    /**
     * Generates Slug
     *
     * @param  mixed $value
     * @return void
     */
    private function generateSlug($value)
    {
        $step1 = str_replace(' ','_',$value);
        $step2 = strtolower($step1);
        $this->slug = $step2; 
    }    
    /**
     * loadModel
     *
     * @return void
     */
    public function loadModel()
    {
         $data = Page::find($this->modelId);
        // dd($data);
        $this->title = $data->title;
        $this->slug = $data->slug;
        $this->content = $data->content; 
    }
    public function mount() 
    {
        //reset pagination after page reload
        $this->resetPage();
    }
    /**
     * the livewire render function
     *
     * @return void
     */
    public function render()
    {
        return view('livewire.pages', [
            'data' => $this->read()
        ]);
    }
}
