@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar for components -->
        <div class="col-md-3 border-end">
            <div class="p-3">
                <h5>Available Components</h5>
                
                <!-- Section Components -->
                <div class="mb-3">
                    <h6>Sections</h6>
                    <div class="list-group">
                        <div class="list-group-item draggable-component" data-type="hero" data-name="Hero Section">
                            Hero Section
                        </div>
                        <div class="list-group-item draggable-component" data-type="features" data-name="Features Section">
                            Features Section
                        </div>
                        <div class="list-group-item draggable-component" data-type="products" data-name="Products Section">
                            Products Section
                        </div>
                        <div class="list-group-item draggable-component" data-type="cta" data-name="Call to Action">
                            Call to Action
                        </div>
                        <div class="list-group-item draggable-component" data-type="newsletter" data-name="Newsletter">
                            Newsletter
                        </div>
                    </div>
                </div>
                
                <!-- Element Components -->
                <div class="mb-3">
                    <h6>Elements</h6>
                    <div class="list-group">
                        <div class="list-group-item draggable-element" data-type="text" data-name="Text Block">
                            Text Block
                        </div>
                        <div class="list-group-item draggable-element" data-type="heading" data-name="Heading">
                            Heading
                        </div>
                        <div class="list-group-item draggable-element" data-type="paragraph" data-name="Paragraph">
                            Paragraph
                        </div>
                        <div class="list-group-item draggable-element" data-type="image" data-name="Image">
                            Image
                        </div>
                        <div class="list-group-item draggable-element" data-type="button" data-name="Button">
                            Button
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content Area -->
        <div class="col-md-9">
            <div class="p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Landing Page Builder</h4>
                    <div>
                        <button id="save-page" class="btn btn-success">Save Changes</button>
                        <a href="{{ route('admin.landing-page-sections.index') }}" class="btn btn-secondary">Back to Sections</a>
                    </div>
                </div>
                
                <div id="page-builder-area" class="border rounded p-3" style="min-height: 500px; background-color: #f8fafc;">
                    <!-- Draggable sections and elements will go here -->
                    @foreach($sections as $section)
                    <div class="section-item" data-id="{{ $section->id }}" data-type="{{ $section->section_type }}">
                        <div class="section-header d-flex justify-content-between align-items-center p-2 bg-primary text-white rounded-top">
                            <span>{{ $section->title ?: $section->name }}</span>
                            <div>
                                <button class="btn btn-sm btn-light edit-section-btn" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-light delete-section-btn" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="section-content p-3 bg-white border rounded-bottom">
                            @if($section->elements->count() > 0)
                                @foreach($section->elements->sortBy('position') as $element)
                                <div class="element-item" data-id="{{ $element->id }}" data-type="{{ $element->element_type }}">
                                    <div class="element-header d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                        <span>{{ ucfirst($element->element_type) }}</span>
                                        <div>
                                            <button class="btn btn-sm btn-outline-secondary edit-element-btn" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger delete-element-btn" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="element-content p-2">
                                        @if($element->element_type === 'image')
                                            @if($element->content)
                                                <img src="{{ $element->content }}" class="img-fluid" alt="Custom Image">
                                            @else
                                                <div class="bg-light border text-center p-3">[Image Placeholder]</div>
                                            @endif
                                        @else
                                            @if($element->content)
                                                {!! Str::limit($element->content, 100, '...') !!}
                                            @else
                                                <em>No content set</em>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted p-4">Drop elements here</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalTitle">Edit Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    @csrf
                    <input type="hidden" id="editItemType">
                    <input type="hidden" id="editItemId">
                    
                    <div class="mb-3">
                        <label for="editItemName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="editItemName">
                    </div>
                    
                    <div class="mb-3" id="editItemTitleContainer">
                        <label for="editItemTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="editItemTitle">
                    </div>
                    
                    <div class="mb-3">
                        <label for="editItemContent" class="form-label">Content</label>
                        <textarea class="form-control" id="editItemContent" rows="5"></textarea>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="editItemActive">
                        <label class="form-check-label" for="editItemActive">Active</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveEditChanges">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">

<script>
$(document).ready(function() {
    // Make sections sortable
    $("#page-builder-area").sortable({
        items: ".section-item",
        handle: ".section-header",
        placeholder: "ui-sortable-placeholder",
        tolerance: "pointer",
        start: function(event, ui) {
            ui.placeholder.height(ui.item.height());
        },
        stop: function(event, ui) {
            // Update positions after sorting sections
            updateSectionPositions();
        }
    });
    
    // Make elements within sections sortable
    $(".section-content").sortable({
        items: ".element-item",
        handle: ".element-header",
        placeholder: "ui-sortable-element-placeholder",
        tolerance: "pointer",
        start: function(event, ui) {
            ui.placeholder.height(ui.item.height());
        },
        stop: function(event, ui) {
            // Update positions after sorting elements
            updateElementPositions($(this).closest('.section-item'));
        }
    });
    
    // Make components draggable
    $(".draggable-component").draggable({
        helper: "clone",
        appendTo: "body",
        cursor: "move",
        zIndex: 1000,
        start: function(event, ui) {
            $(ui.helper).addClass("dragging");
        }
    });
    
    $(".draggable-element").draggable({
        helper: "clone",
        appendTo: "body",
        cursor: "move",
        zIndex: 1000,
        start: function(event, ui) {
            $(ui.helper).addClass("dragging");
        }
    });
    
    // Make section-content droppable for elements
    $(".section-content").droppable({
        accept: ".draggable-element",
        hoverClass: "drop-hover",
        drop: function(event, ui) {
            const elementType = ui.draggable.data('type');
            const elementName = ui.draggable.data('name');
            
            const newElementHtml = `
                <div class="element-item" data-type="${elementType}">
                    <div class="element-header d-flex justify-content-between align-items-center p-2 bg-light rounded">
                        <span>${elementName}</span>
                        <div>
                            <button class="btn btn-sm btn-outline-secondary edit-element-btn" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-element-btn" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="element-content p-2">
                        <em>New ${elementName} - Edit to add content</em>
                    </div>
                </div>
            `;
            
            $(this).append(newElementHtml);
            
            // Add event listeners to the new element
            setupElementEventListeners($(this).find('.element-item').last());
        }
    });
    
    // Make page builder area droppable for sections
    $("#page-builder-area").droppable({
        accept: ".draggable-component",
        hoverClass: "drop-hover",
        drop: function(event, ui) {
            const sectionType = ui.draggable.data('type');
            const sectionName = ui.draggable.data('name');
            
            const newSectionHtml = `
                <div class="section-item" data-type="${sectionType}">
                    <div class="section-header d-flex justify-content-between align-items-center p-2 bg-primary text-white rounded-top">
                        <span>${sectionName}</span>
                        <div>
                            <button class="btn btn-sm btn-light edit-section-btn" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-light delete-section-btn" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="section-content p-3 bg-white border rounded-bottom">
                        <div class="text-center text-muted p-4">Drop elements here</div>
                    </div>
                </div>
            `;
            
            $(this).append(newSectionHtml);
            
            // Initialize sortable for the new section's content
            const newSectionContent = $(this).find('.section-content').last();
            newSectionContent.sortable({
                items: ".element-item",
                handle: ".element-header",
                placeholder: "ui-sortable-element-placeholder",
                tolerance: "pointer",
                start: function(event, ui) {
                    ui.placeholder.height(ui.item.height());
                },
                stop: function(event, ui) {
                    updateElementPositions($(this).closest('.section-item'));
                }
            });
            
            // Add event listeners to the new section
            setupSectionEventListeners(newSectionContent.closest('.section-item'));
        }
    });
    
    // Setup event listeners for existing sections
    $('.section-item').each(function() {
        setupSectionEventListeners($(this));
    });
    
    // Setup event listeners for existing elements
    $('.element-item').each(function() {
        setupElementEventListeners($(this));
    });
    
    // Save page changes
    $("#save-page").click(function() {
        // Update positions first
        updateSectionPositions();
        
        // Collect all sections and elements data
        const pageData = collectPageData();
        
        $.ajax({
            url: '{{ route("admin.landing-page-sections.sort") }}',
            type: 'POST',
            data: {
                sections: pageData.sections,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.success) {
                    alert('Page saved successfully!');
                }
            },
            error: function(xhr) {
                console.error('Error saving page:', xhr);
                alert('Error saving page. Please try again.');
            }
        });
    });
    
    // Setup section event listeners
    function setupSectionEventListeners(sectionItem) {
        // Edit section button
        sectionItem.find('.edit-section-btn').off('click').on('click', function() {
            const section = $(this).closest('.section-item');
            const sectionType = section.data('type');
            const sectionTitle = section.find('.section-header span').text();
            
            $('#editModalTitle').text('Edit Section');
            $('#editItemType').val('section');
            $('#editItemId').val(section.data('id') || '');
            $('#editItemName').val(sectionTitle);
            $('#editItemTitle').val(sectionTitle);
            $('#editItemContent').val(section.find('.section-content').html());
            $('#editItemActive').prop('checked', true); // assuming active by default
            
            $('#editModal').modal('show');
        });
        
        // Delete section button
        sectionItem.find('.delete-section-btn').off('click').on('click', function() {
            if(confirm('Are you sure you want to delete this section and all its elements?')) {
                $(this).closest('.section-item').remove();
            }
        });
    }
    
    // Setup element event listeners
    function setupElementEventListeners(elementItem) {
        // Edit element button
        elementItem.find('.edit-element-btn').off('click').on('click', function() {
            const element = $(this).closest('.element-item');
            const elementType = element.data('type');
            const elementContent = element.find('.element-content').html();
            
            $('#editModalTitle').text('Edit Element');
            $('#editItemType').val('element');
            $('#editItemId').val(element.data('id') || '');
            $('#editItemName').val(element.find('.element-header span').text());
            $('#editItemTitleContainer').hide(); // hide title field for elements
            $('#editItemContent').val(elementContent);
            $('#editItemActive').prop('checked', true); // assuming active by default
            
            $('#editModal').modal('show');
        });
        
        // Delete element button
        elementItem.find('.delete-element-btn').off('click').on('click', function() {
            if(confirm('Are you sure you want to delete this element?')) {
                $(this).closest('.element-item').remove();
            }
        });
    }
    
    // Update section positions
    function updateSectionPositions() {
        $('.section-item').each(function(index) {
            // In a real implementation, you'd update the position in the database
            console.log(`Section ${$(this).data('id')} at position ${index}`);
        });
    }
    
    // Update element positions within a section
    function updateElementPositions(sectionItem) {
        const sectionContent = sectionItem.find('.section-content');
        sectionContent.find('.element-item').each(function(index) {
            // In a real implementation, you'd update the position in the database
            console.log(`Element ${$(this).data('id')} in section at position ${index}`);
        });
    }
    
    // Collect page data
    function collectPageData() {
        const pageData = {
            sections: []
        };
        
        $('.section-item').each(function(index) {
            const sectionId = $(this).data('id');
            if(sectionId) {
                pageData.sections.push(sectionId);
            }
        });
        
        return pageData;
    }
    
    // Save edit changes
    $('#saveEditChanges').click(function() {
        const itemType = $('#editItemType').val();
        const itemId = $('#editItemId').val();
        const name = $('#editItemName').val();
        const title = $('#editItemTitle').val();
        const content = $('#editItemContent').val();
        const isActive = $('#editItemActive').is(':checked');
        
        if(itemType === 'section') {
            // Update section
            const section = $(`.section-item[data-id="${itemId}"]`);
            if(section.length) {
                section.find('.section-header span').text(title || name);
                section.find('.section-content').html(content || '<div class="text-center text-muted p-4">Drop elements here</div>');
            }
        } else if(itemType === 'element') {
            // Update element
            const element = $(`.element-item[data-id="${itemId}"]`);
            if(element.length) {
                element.find('.element-header span').text(name);
                element.find('.element-content').html(content);
            }
        }
        
        $('#editModal').modal('hide');
    });
});

// Custom styles for drag and drop
$(document).ready(function() {
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .ui-sortable-placeholder {
                border: 2px dashed #0d6efd;
                background: rgba(13, 110, 253, 0.1);
                visibility: visible !important;
                height: 50px;
            }
            .ui-sortable-element-placeholder {
                border: 2px dashed #198754;
                background: rgba(25, 135, 84, 0.1);
                visibility: visible !important;
                height: 30px;
            }
            .drop-hover {
                border: 2px dashed #0d6efd;
            }
            .section-item {
                margin-bottom: 15px;
                border: 1px solid #dee2e6;
                border-radius: 0.375rem;
            }
            .element-item {
                margin-bottom: 10px;
                border: 1px solid #e9ecef;
                border-radius: 0.375rem;
            }
        `)
        .appendTo('head');
});
</script>
@endsection