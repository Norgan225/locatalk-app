<x-app-layout>
    <style>
        .create-container {
            padding: 2rem;
            max-width: 900px;
            margin: 0 auto;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, #ffffff, rgba(255, 255, 255, 0.8));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
        }

        .form-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 0.5rem;
        }

        .form-label.required::after {
            content: '*';
            color: #ef4444;
            margin-left: 4px;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            color: #ffffff;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: #df5526;
            background: rgba(255, 255, 255, 0.08);
        }

        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-hint {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 0.5rem;
        }

        .error-message {
            color: #ef4444;
            font-size: 12px;
            margin-top: 0.5rem;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(223, 85, 38, 0.3);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.9);
        }

        .color-picker-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(50px, 1fr));
            gap: 0.75rem;
            margin-top: 0.5rem;
        }

        .color-option {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            cursor: pointer;
            border: 3px solid transparent;
            transition: all 0.3s ease;
            position: relative;
        }

        .color-option:hover {
            transform: scale(1.1);
        }

        .color-option.selected {
            border-color: #ffffff;
            transform: scale(1.1);
        }

        .color-option.selected::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #ffffff;
            font-weight: bold;
            font-size: 18px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        @media (max-width: 768px) {
            .create-container {
                padding: 1rem;
            }

            .form-card {
                padding: 1.5rem;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <div class="create-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">{{ org_trans('create') }} {{ org_trans('department') }}</h1>
            <p class="page-subtitle">{{ org_trans('add_new_department_to_organization') }}</p>
        </div>

        <!-- Form Card -->
        <div class="form-card">
            <form action="{{ route('web.departments.store') }}" method="POST">
                @csrf

                <!-- Organization -->
                @if(auth()->user()->isSuperAdmin() && $organizations->count() > 1)
                <div class="form-group">
                    <label for="organization_id" class="form-label required">{{ org_trans('organization') }}</label>
                    <select name="organization_id" id="organization_id" class="form-select" required>
                        <option value="">{{ org_trans('select_organization') }}</option>
                        @foreach($organizations as $org)
                        <option value="{{ $org->id }}" {{ old('organization_id') == $org->id ? 'selected' : '' }}>
                            {{ $org->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('organization_id')
                    <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                @else
                <input type="hidden" name="organization_id" value="{{ auth()->user()->organization_id }}">
                @endif

                <!-- Name -->
                <div class="form-group">
                    <label for="name" class="form-label required">{{ org_trans('name') }} {{ org_trans('department') }}</label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-input"
                        value="{{ old('name') }}"
                        placeholder="Ex: Ressources Humaines, IT, Marketing..."
                        required
                    >
                    @error('name')
                    <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description" class="form-label">{{ org_trans('description') }}</label>
                    <textarea
                        name="description"
                        id="description"
                        class="form-textarea"
                        placeholder="{{ org_trans('describe_department_purpose') }}"
                    >{{ old('description') }}</textarea>
                    @error('description')
                    <p class="error-message">{{ $message }}</p>
                    @enderror
                    <p class="form-hint">{{ org_trans('optional_max_1000_chars') }}</p>
                </div>

                <!-- Color -->
                <div class="form-group">
                    <label class="form-label">{{ org_trans('department_color') }}</label>
                    <input type="hidden" name="color" id="color_input" value="{{ old('color', '#df5526') }}">
                    <div class="color-picker-grid">
                        <div class="color-option selected" style="background: linear-gradient(135deg, #df5526, #fbbb2a);" onclick="selectColor('linear-gradient(135deg, #df5526, #fbbb2a)')"></div>
                        <div class="color-option" style="background: linear-gradient(135deg, #3b82f6, #2563eb);" onclick="selectColor('linear-gradient(135deg, #3b82f6, #2563eb)')"></div>
                        <div class="color-option" style="background: linear-gradient(135deg, #10b981, #059669);" onclick="selectColor('linear-gradient(135deg, #10b981, #059669)')"></div>
                        <div class="color-option" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);" onclick="selectColor('linear-gradient(135deg, #8b5cf6, #7c3aed)')"></div>
                        <div class="color-option" style="background: linear-gradient(135deg, #ec4899, #db2777);" onclick="selectColor('linear-gradient(135deg, #ec4899, #db2777)')"></div>
                        <div class="color-option" style="background: linear-gradient(135deg, #f59e0b, #d97706);" onclick="selectColor('linear-gradient(135deg, #f59e0b, #d97706)')"></div>
                        <div class="color-option" style="background: linear-gradient(135deg, #ef4444, #dc2626);" onclick="selectColor('linear-gradient(135deg, #ef4444, #dc2626)')"></div>
                        <div class="color-option" style="background: linear-gradient(135deg, #06b6d4, #0891b2);" onclick="selectColor('linear-gradient(135deg, #06b6d4, #0891b2)')"></div>
                    </div>
                    @error('color')
                    <p class="error-message">{{ $message }}</p>
                    @enderror
                    <p class="form-hint">{{ org_trans('choose_color_for_department') }}</p>
                </div>

                <!-- Is Active -->
                <div class="form-group">
                    <label class="form-label">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} style="margin-right: 0.5rem;">
                        {{ org_trans('department_active') }}
                    </label>
                    <p class="form-hint">{{ org_trans('inactive_departments_not_visible') }}</p>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('web.departments') }}" class="btn btn-secondary">
                        {{ org_trans('cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ org_trans('create') }} {{ org_trans('department') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function selectColor(color) {
            // Remove selected class from all options
            document.querySelectorAll('.color-option').forEach(opt => {
                opt.classList.remove('selected');
            });

            // Add selected class to clicked option
            event.target.classList.add('selected');

            // Update hidden input
            document.getElementById('color_input').value = color;
        }
    </script>
</x-app-layout>
