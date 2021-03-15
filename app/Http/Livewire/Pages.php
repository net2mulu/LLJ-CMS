<?php

namespace App\Http\Livewire;

use Illuminate\Validation\Rule;
use App\Models\Page;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class Pages extends Component
{
    use WithPagination;

    public $slug , $title, $content, $modelId ,$confirmingUserDeletion ,$isSetToDefaultHomePage, $isSetToDefaultNotFoundPage;
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
        $this->slug = Str::slug($value);
    }
    /**
     * create new record in db
     *
     * @return void
     */
    public function create()
    {
        $this->validate();
        $this->unassignDefaultHomePage();
        $this->unassignDefaultNotFoundPage();
        $create = Page::create($this->modelData());
        if(!$create){dd($create);}
        $this->modalFormVisible = false;
        $this->reset();
    }       
    /**
     * update
     *
     * @return void
     */
    public function update()
    {
        $this->validate();
        $this->unassignDefaultHomePage();
        $this->unassignDefaultNotFoundPage();
        Page::find($this->modelId)->update($this->modelData());
        $this->modalFormVisible = false;
    } 

    public function delete()
    {
        //dd('deleting');
        Page::destroy($this->modelId);
        $this->modalConfirmDeleteVisible = false;
        $this->reset();

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
        $this->reset();
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
       return Page::latest()->paginate(5);
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
        $this->reset();
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
            'content' => $this->content,
            'is_default_home' => $this->isSetToDefaultHomePage,
            'is_default_not_found' => $this->isSetToDefaultNotFoundPage
        ];
    }
    
    /**
     * after the insertion this function is called for reseting the values of
     * the variables.
     *
     * @return void
     */
    // public function resetrVars() 
    // {
    //     $this->modelId = null;
    //     $this->title = null;
    //     $this->slug = null;
    //     $this->content = null;
    //     $this->isSetToDefaultHomePage = null;
    //     $this->isSetToDefaultNotFoundPage = null;
    // }
    
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
        $this->isSetToDefaultNotFoundPage = !$data->is_default_not_found ? null:true;
        $this->isSetToDefaultHomePage = !$data->is_default_home ? null:true;
    }
    public function mount() 
    {
        //reset pagination after page reload
        $this->resetPage();
    }
    
    /**
     * updatedIsSetToDefaultHomePage uses updated lifcyclehook
     *
     * @return void
     */
    public function updatedIsSetToDefaultHomePage()
    {
        $this->isSetToDefaultNotFoundPage = null;
    }

    public function updatedIsSetToDefaultNotFoundPage()
    {
        $this->isSetToDefaultHomePage = null;
    }
    private function unassignDefaultHomePage()
    {
        if($this->isSetToDefaultHomePage != null) {
            Page::where('is_default_home' , true)->update([
                'is_default_home' => false,
            ]);
        }
    }

    private function unassignDefaultNotFoundPage()
    {
        if($this->isSetToDefaultNotFoundPage != null) {
            Page::where('is_default_not_found' , true)->update([
                'is_default_not_found' => false,
            ]);
        }
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
