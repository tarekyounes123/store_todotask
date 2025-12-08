@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Site Settings') }}</span>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.site-settings.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Website Title Section -->
                        <div class="mb-4 p-3 border rounded">
                            <h4>Website Title</h4>

                            <div class="mb-3">
                                <label for="app_name" class="form-label">Website Name</label>
                                <input type="text" class="form-control" id="app_name" name="app_name"
                                       value="{{ old('app_name', $titleSetting->setting_value['app_name'] ?? config('app.name', 'ToDoTask')) }}">
                                <small class="form-text text-muted">This will be displayed as the website title and in the navigation bar.</small>
                            </div>
                        </div>

                        <!-- Logo and Favicon Section -->
                        <div class="mb-4 p-3 border rounded">
                            <h4>Logo and Favicon</h4>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="logo" class="form-label">Logo</label>
                                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                        <small class="form-text text-muted">Recommended format: PNG, JPG, SVG. Max size: 2MB.</small>
                                        @if(isset($logoSetting->setting_value['logo']) && $logoSetting->setting_value['logo'])
                                            <div class="mt-2">
                                                <p>Current Logo:</p>
                                                <img src="{{ asset('storage/' . $logoSetting->setting_value['logo']) }}" alt="Current Logo" style="max-height: 100px; max-width: 200px;">
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="favicon" class="form-label">Favicon</label>
                                        <input type="file" class="form-control" id="favicon" name="favicon" accept=".ico,.png,.jpg,.jpeg">
                                        <small class="form-text text-muted">Recommended format: ICO, PNG. Max size: 2MB.</small>
                                        @if(file_exists(public_path('favicon.ico')))
                                            <div class="mt-2">
                                                <p>Current Favicon:</p>
                                                <img src="{{ asset('favicon.ico') }}?v={{ time() }}" alt="Current Favicon" style="max-height: 32px; max-width: 32px;">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Company Info Section -->
                        <div class="mb-4 p-3 border rounded">
                            <h4>Company Information</h4>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="company_info_name" class="form-label">Company Name</label>
                                        <input type="text" class="form-control" id="company_info_name" name="company_info[name]"
                                               value="{{ old('company_info.name', $footerSetting->setting_value['company_info']['name'] ?? '') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="company_info_email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="company_info_email" name="company_info[email]"
                                               value="{{ old('company_info.email', $footerSetting->setting_value['company_info']['email'] ?? '') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="company_info_description" class="form-label">Description</label>
                                <textarea class="form-control" id="company_info_description" name="company_info[description]" rows="3">{{ old('company_info.description', $footerSetting->setting_value['company_info']['description'] ?? '') }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="company_info_address" class="form-label">Address</label>
                                        <input type="text" class="form-control" id="company_info_address" name="company_info[address]"
                                               value="{{ old('company_info.address', $footerSetting->setting_value['company_info']['address'] ?? '') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="company_info_phone" class="form-label">Phone</label>
                                        <input type="text" class="form-control" id="company_info_phone" name="company_info[phone]"
                                               value="{{ old('company_info.phone', $footerSetting->setting_value['company_info']['phone'] ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Social Links Section -->
                        <div class="mb-4 p-3 border rounded">
                            <h4>Social Media Links</h4>

                            <div id="social-links-container">
                                @if(isset($footerSetting->setting_value['social_links']) && is_array($footerSetting->setting_value['social_links']))
                                    @foreach($footerSetting->setting_value['social_links'] as $index => $social)
                                        <div class="social-link-item mb-3">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" name="social_links[{{ $index }}][name]"
                                                           placeholder="Platform Name" value="{{ $social['name'] ?? '' }}">
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="text" class="form-control" name="social_links[{{ $index }}][url]"
                                                           placeholder="URL" value="{{ $social['url'] ?? '#' }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" name="social_links[{{ $index }}][icon]"
                                                           placeholder="Icon Class" value="{{ $social['icon'] ?? '' }}">
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-danger btn-sm remove-social-link">X</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="social-link-item mb-3">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input type="text" class="form-control" name="social_links[0][name]"
                                                       placeholder="Platform Name" value="Facebook">
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="social_links[0][url]"
                                                       placeholder="URL" value="#">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" class="form-control" name="social_links[0][icon]"
                                                       placeholder="Icon Class" value="fab fa-facebook-f">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger btn-sm remove-social-link">X</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="social-link-item mb-3">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input type="text" class="form-control" name="social_links[1][name]"
                                                       placeholder="Platform Name" value="Twitter">
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="social_links[1][url]"
                                                       placeholder="URL" value="#">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" class="form-control" name="social_links[1][icon]"
                                                       placeholder="Icon Class" value="fab fa-twitter">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger btn-sm remove-social-link">X</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="social-link-item mb-3">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input type="text" class="form-control" name="social_links[2][name]"
                                                       placeholder="Platform Name" value="Instagram">
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="social_links[2][url]"
                                                       placeholder="URL" value="#">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" class="form-control" name="social_links[2][icon]"
                                                       placeholder="Icon Class" value="fab fa-instagram">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger btn-sm remove-social-link">X</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="social-link-item mb-3">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input type="text" class="form-control" name="social_links[3][name]"
                                                       placeholder="Platform Name" value="YouTube">
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="social_links[3][url]"
                                                       placeholder="URL" value="#">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" class="form-control" name="social_links[3][icon]"
                                                       placeholder="Icon Class" value="fab fa-youtube">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger btn-sm remove-social-link">X</button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <button type="button" class="btn btn-secondary btn-sm" id="add-social-link">Add Social Link</button>
                        </div>

                        <!-- Shop Links Section -->
                        <div class="mb-4 p-3 border rounded">
                            <h4>Shop Links</h4>

                            <div id="shop-links-container">
                                @if(isset($footerSetting->setting_value['shop_links']) && is_array($footerSetting->setting_value['shop_links']))
                                    @foreach($footerSetting->setting_value['shop_links'] as $index => $link)
                                        <div class="shop-link-item mb-3">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <input type="text" class="form-control" name="shop_links[{{ $index }}][name]"
                                                           placeholder="Link Text" value="{{ $link['name'] ?? '' }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="shop_links[{{ $index }}][url]"
                                                           placeholder="URL" value="{{ $link['url'] ?? '#' }}">
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-danger btn-sm remove-shop-link">X</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="shop-link-item mb-3">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="shop_links[0][name]"
                                                       placeholder="Link Text" value="All Products">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="shop_links[0][url]"
                                                       placeholder="URL" value="/products">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger btn-sm remove-shop-link">X</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="shop-link-item mb-3">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="shop_links[1][name]"
                                                       placeholder="Link Text" value="Featured Items">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="shop_links[1][url]"
                                                       placeholder="URL" value="#">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger btn-sm remove-shop-link">X</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="shop-link-item mb-3">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="shop_links[2][name]"
                                                       placeholder="Link Text" value="New Arrivals">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="shop_links[2][url]"
                                                       placeholder="URL" value="#">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger btn-sm remove-shop-link">X</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="shop-link-item mb-3">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="shop_links[3][name]"
                                                       placeholder="Link Text" value="Best Sellers">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="shop_links[3][url]"
                                                       placeholder="URL" value="#">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger btn-sm remove-shop-link">X</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="shop-link-item mb-3">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="shop_links[4][name]"
                                                       placeholder="Link Text" value="Sale">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="shop_links[4][url]"
                                                       placeholder="URL" value="#">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger btn-sm remove-shop-link">X</button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <button type="button" class="btn btn-secondary btn-sm" id="add-shop-link">Add Shop Link</button>
                        </div>

                        <!-- Company Links Section -->
                        <div class="mb-4 p-3 border rounded">
                            <h4>Company Links</h4>

                            <div id="company-links-container">
                                @if(isset($footerSetting->setting_value['company_links']) && is_array($footerSetting->setting_value['company_links']))
                                    @foreach($footerSetting->setting_value['company_links'] as $index => $link)
                                        <div class="company-link-item mb-3">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <input type="text" class="form-control" name="company_links[{{ $index }}][name]"
                                                           placeholder="Link Text" value="{{ $link['name'] ?? '' }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="company_links[{{ $index }}][url]"
                                                           placeholder="URL" value="{{ $link['url'] ?? '#' }}">
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-danger btn-sm remove-company-link">X</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="company-link-item mb-3">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="company_links[0][name]"
                                                       placeholder="Link Text" value="About Us">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="company_links[0][url]"
                                                       placeholder="URL" value="#">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger btn-sm remove-company-link">X</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="company-link-item mb-3">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="company_links[1][name]"
                                                       placeholder="Link Text" value="Contact">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="company_links[1][url]"
                                                       placeholder="URL" value="#">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger btn-sm remove-company-link">X</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="company-link-item mb-3">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="company_links[2][name]"
                                                       placeholder="Link Text" value="Careers">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="company_links[2][url]"
                                                       placeholder="URL" value="#">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger btn-sm remove-company-link">X</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="company-link-item mb-3">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="company_links[3][name]"
                                                       placeholder="Link Text" value="Blog">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="company_links[3][url]"
                                                       placeholder="URL" value="#">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger btn-sm remove-company-link">X</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="company-link-item mb-3">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="company_links[4][name]"
                                                       placeholder="Link Text" value="Press">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="company_links[4][url]"
                                                       placeholder="URL" value="#">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger btn-sm remove-company-link">X</button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <button type="button" class="btn btn-secondary btn-sm" id="add-company-link">Add Company Link</button>
                        </div>

                        <!-- Featured Products Selection -->
                        <div class="mb-4 p-3 border rounded">
                            <h4>Featured Products</h4>
                            <p class="text-muted">Select products to display in the featured products section on the landing page</p>

                            <div class="row">
                                <div class="col-md-12">
                                    <select name="featured_product_ids[]" id="featured_product_ids" class="form-control" multiple size="10">
                                        @foreach($allProducts as $product)
                                            <option value="{{ $product->id }}"
                                                {{ in_array($product->id, $featuredProductsSetting->setting_value['product_ids'] ?? []) ? 'selected' : '' }}>
                                                {{ $product->name }} (ID: {{ $product->id }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple products. Maximum 8 recommended.</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                            <a href="{{ url('/') }}" class="btn btn-secondary">Preview Website</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add social link
    document.getElementById('add-social-link').addEventListener('click', function() {
        const container = document.getElementById('social-links-container');
        const index = container.children.length;
        const newLink = document.createElement('div');
        newLink.className = 'social-link-item mb-3';
        newLink.innerHTML = `
            <div class="row">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="social_links[${index}][name]"
                           placeholder="Platform Name">
                </div>
                <div class="col-md-5">
                    <input type="text" class="form-control" name="social_links[${index}][url]"
                           placeholder="URL" value="#">
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="social_links[${index}][icon]"
                           placeholder="Icon Class">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-social-link">X</button>
                </div>
            </div>
        `;
        container.appendChild(newLink);

        // Add event to the new remove button
        newLink.querySelector('.remove-social-link').addEventListener('click', function() {
            newLink.remove();
        });
    });

    // Add shop link
    document.getElementById('add-shop-link').addEventListener('click', function() {
        const container = document.getElementById('shop-links-container');
        const index = container.children.length;
        const newLink = document.createElement('div');
        newLink.className = 'shop-link-item mb-3';
        newLink.innerHTML = `
            <div class="row">
                <div class="col-md-5">
                    <input type="text" class="form-control" name="shop_links[${index}][name]"
                           placeholder="Link Text">
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="shop_links[${index}][url]"
                           placeholder="URL" value="#">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-shop-link">X</button>
                </div>
            </div>
        `;
        container.appendChild(newLink);

        // Add event to the new remove button
        newLink.querySelector('.remove-shop-link').addEventListener('click', function() {
            newLink.remove();
        });
    });

    // Add company link
    document.getElementById('add-company-link').addEventListener('click', function() {
        const container = document.getElementById('company-links-container');
        const index = container.children.length;
        const newLink = document.createElement('div');
        newLink.className = 'company-link-item mb-3';
        newLink.innerHTML = `
            <div class="row">
                <div class="col-md-5">
                    <input type="text" class="form-control" name="company_links[${index}][name]"
                           placeholder="Link Text">
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="company_links[${index}][url]"
                           placeholder="URL" value="#">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-company-link">X</button>
                </div>
            </div>
        `;
        container.appendChild(newLink);

        // Add event to the new remove button
        newLink.querySelector('.remove-company-link').addEventListener('click', function() {
            newLink.remove();
        });
    });

    // Add event listeners to existing remove buttons
    document.querySelectorAll('.remove-social-link').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.social-link-item').remove();
        });
    });

    document.querySelectorAll('.remove-shop-link').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.shop-link-item').remove();
        });
    });

    document.querySelectorAll('.remove-company-link').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.company-link-item').remove();
        });
    });
});
</script>
@endsection