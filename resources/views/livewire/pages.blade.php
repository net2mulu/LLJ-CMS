<div class="p-6">
    {{-- create button --}}
    <div class="items-center justify-end px-4 py-3 text-right felx sm:px-6">
            <x-jet-button wire:click="createShowModal">
                {{ __('Create') }}
            </x-jet-button>
        </div>

        {{-- table --}}
        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <div class="overflow-hidden border-b border-gray-200 shadow sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">Title</th>
                                    <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">Link</th>
                                    <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">Content</th>
                                    <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @if ($data->count())
                                    @foreach ($data as $item)
                                        <tr>
                                            <td class="px-6 py-4 text-sm whitespace-no-wrap">
                                                {{ $item->title }}
                                                {!! $item->is_default_home ? '<span class="text-xs font-bold text-green-400">[Default Home Page]</span>':''!!}
                                                {!! $item->is_default_not_found ? '<span class="text-xs font-bold text-red-400">[Default 404 Page]</span>':''!!}
                                            </td>
                                            <td class="px-6 py-4 text-sm whitespace-no-wrap">
                                                <a
                                                    class="text-indigo-600 hover:text-indigo-900"
                                                    target="_blank"
                                                    href="{{ URL::to('/'.$item->slug)}}"
                                                >
                                                    {{ $item->slug }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 text-sm whitespace-no-wrap">{!! \Illuminate\Support\Str::limit($item->content, 50, '...') !!}</td>
                                            <td class="px-6 py-4 text-sm text-right">
                                                <x-jet-button wire:click="updateShowModal({{ $item->id }})">
                                                    {{ __('Update') }}
                                                </x-jet-button>
                                                <x-jet-danger-button wire:click="deleteShowModal({{ $item->id }})">
                                                    {{ __('Delete') }}
                                                </x-jet-button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="px-6 py-4 text-sm whitespace-no-wrap" colspan="4">No Results Found</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <br/>
        {{ $data->links() }}
        {{-- modal form --}}
            <x-jet-dialog-modal wire:model="modalFormVisible">
                <x-slot name="title">
                    {{ __('Save Page') }} {{ $modelId }}
                </x-slot>

                <x-slot name="content">

                    <div class="mt-4">
                        <x-jet-label for="title" value="{{ __('Title') }}" />
                        <x-jet-input id="title" class="block w-full mt-1" type="text" wire:model="title" />
                        @error('title') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="mt-4">
                        <x-jet-label for="title" value="{{ __('Slug') }}" />
                        <div class="flex mt-1 rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 text-sm border border-r-0 rounded-1-md border-grey-300 border-grey-50 text-grey-500">
                                http://localhost:8000
                            </span>
                            <input wire:model="slug" class="flex-1 block w-full transition duration-150 ease-in-out rounded-none form-input rounded-r-md sm:text-sm sm:leading-5" aria-placeholder="url-slug" />
                        </div>
                        @error('slug') <span class="error">{{ $message }}</span> @enderror

                    </div>
                    
                    <div class="mt-4">
                        <input type="checkbox" class="form-checkbox" value="{{ $isSetToDefaultHomePage }}" wire:model="isSetToDefaultHomePage">
                        <span class="ml-2 text-sm text-grey-600">set as the default home page</span>
                    </div>

                    <div class="mt-4">
                        <input type="checkbox" class="form-checkbox" value="{{ $isSetToDefaultNotFoundPage }}" wire:model="isSetToDefaultNotFoundPage">
                        <span class="ml-2 text-sm text-red-600">set as the default not found page</span>
                    </div>
                    
                    <div class="mt-4">
                        <x-jet-label for="title" value="{{ __('Content') }}" />
                        <div class="rounded-md shadow-sm">
                            <div class="mt-1 bg-white">
                                <div class="body-content" wire:ignore>
                                    <trix-editor
                                        class="trix-content"
                                        x-ref="trix"
                                        wire:model.debounce.100000ms="content"
                                        wire:key="trix-content-unique-key"
                                    ></trix-editor>
                                </div>
                            </div>
                        </div>
                        @error('content') <span class="error">{{ $message }}</span> @enderror

                    </div>

                </x-slot>

                <x-slot name="footer">
                    <x-jet-secondary-button wire:loading.attr="disabled">
                        {{ __('Cancel') }}
                    </x-jet-secondary-button>
                    @if ($modelId)
                    <x-jet-danger-button class="ml-2" wire:click="update" wire:loading.attr="disabled">
                        {{ __('Update') }}
                    </x-jet-danger-button>
                    @else
                    <x-jet-danger-button class="ml-2" wire:click="create" wire:loading.attr="disabled">
                        {{ __('Create') }}
                    </x-jet-danger-button> 
                    @endif
                    
                </x-slot>
            </x-jet-dialog-modal>

                {{-- delete --}}

                <x-jet-dialog-modal wire:model="modalConfirmDeleteVisible">
                    <x-slot name="title">
                        {{ __('Delete Account') }}
                    </x-slot>
        
                    <x-slot name="content">
                        {{ __('Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                    </x-slot>
        
                    <x-slot name="footer">
                        <x-jet-secondary-button wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled">
                            {{ __('Cancel') }}
                        </x-jet-secondary-button>
        
                        <x-jet-danger-button class="ml-2" wire:click="delete" wire:loading.attr="disabled">
                            {{ __('Delete Account') }}
                        </x-jet-danger-button>
                    </x-slot>
                </x-jet-dialog-modal>
</div>
