<x-app-layout>
<div class="p-4 sm:p-6 lg:p-8 max-w-4xl mx-auto">
	<!-- Header -->
	<div class="mb-8">
		<a href="{{ route('web.meetings.show', $meeting->id) }}" class="inline-flex items-center text-sm text-gray-600 hover:text-orange-600 transition-colors mb-4">
			<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
			</svg>
			{{ org_trans('back_to_details') }}
		</a>
		<h1 class="text-2xl md:text-3xl font-bold text-gray-900">✏️ {{ org_trans('edit_meeting') }}</h1>
		<p class="mt-1 text-sm text-gray-600">{{ org_trans('update_meeting_info') }}</p>
	</div>

	<!-- Form -->
	<form action="{{ route('web.meetings.update', $meeting->id) }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
		@csrf
		@method('PUT')

		<!-- Title -->
		<div class="mb-6">
			<label for="title" class="block text-sm font-semibold text-gray-900 mb-2">
				{{ org_trans('meeting_title') }} <span class="text-red-600">*</span>
			</label>
			<input
				type="text"
				id="title"
				name="title"
				value="{{ old('title', $meeting->title) }}"
				required
				class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('title') border-red-500 @enderror"
				placeholder="Ex: Daily Standup, Sprint Planning..."
			>
			@error('title')
			<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
			@enderror
		</div>

		<!-- Description -->
		<div class="mb-6">
			<label for="description" class="block text-sm font-semibold text-gray-900 mb-2">
				Description
			</label>
			<textarea
				id="description"
				name="description"
				rows="4"
				class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('description') border-red-500 @enderror"
				placeholder="Décrivez l'objectif et l'ordre du jour de la réunion..."
			>{{ old('description', $meeting->description) }}</textarea>
			@error('description')
			<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
			@enderror
		</div>

		<!-- Status -->
		<div class="mb-6">
			<label for="status" class="block text-sm font-semibold text-gray-900 mb-2">
				{{ org_trans('status') }} <span class="text-red-600">*</span>
			</label>
			<select
				id="status"
				name="status"
				required
				class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('status') border-red-500 @enderror"
			>
				<option value="scheduled" {{ old('status', $meeting->status) == 'scheduled' ? 'selected' : '' }}>{{ org_trans('scheduled') }}</option>
				<option value="ongoing" {{ old('status', $meeting->status) == 'ongoing' ? 'selected' : '' }}>{{ org_trans('ongoing') }}</option>
				<option value="completed" {{ old('status', $meeting->status) == 'completed' ? 'selected' : '' }}>{{ org_trans('completed') }}</option>
				<option value="cancelled" {{ old('status', $meeting->status) == 'cancelled' ? 'selected' : '' }}>{{ org_trans('cancelled') }}</option>
			</select>
			@error('status')
			<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
			@enderror
		</div>

		<!-- Date & Time -->
		<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
			<!-- Start Date/Time -->
			<div>
				<label for="start_time" class="block text-sm font-semibold text-gray-900 mb-2">
					{{ org_trans('start_date_time') }} <span class="text-red-600">*</span>
				</label>
				<input
					type="datetime-local"
					id="start_time"
					name="start_time"
					value="{{ old('start_time', $meeting->start_time->format('Y-m-d\TH:i')) }}"
					required
					class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('start_time') border-red-500 @enderror"
				>
				@error('start_time')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<!-- End Date/Time -->
			<div>
				<label for="end_time" class="block text-sm font-semibold text-gray-900 mb-2">
					{{ org_trans('end_date_time') }}
				</label>
				<input
					type="datetime-local"
					id="end_time"
					name="end_time"
					value="{{ old('end_time', $meeting->end_time ? $meeting->end_time->format('Y-m-d\TH:i') : '') }}"
					class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('end_time') border-red-500 @enderror"
				>
				@error('end_time')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>
		</div>

		<!-- Participants -->
		<div class="mb-6">
			<label for="participants" class="block text-sm font-semibold text-gray-900 mb-2">
				{{ org_trans('participants') }}
			</label>
			<select
				id="participants"
				name="participants[]"
				multiple
				class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('participants') border-red-500 @enderror"
				style="min-height: 200px;"
			>
				@foreach($users as $user)
				<option value="{{ $user->id }}"
					{{ (collect(old('participants', $meeting->participants->pluck('id')->toArray()))->contains($user->id)) ? 'selected' : '' }}
				>
					{{ $user->name }} ({{ $user->email }})
				</option>
				@endforeach
			</select>
			<p class="mt-1 text-xs text-gray-500">{{ org_trans('select_participants_hint') }}</p>
			@error('participants')
			<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
			@enderror
		</div>

		<!-- Recording Option -->
		<div class="mb-6">
			<div class="flex items-start">
				<div class="flex items-center h-5">
					<input
						type="checkbox"
						id="is_recorded"
						name="is_recorded"
						value="1"
						{{ old('is_recorded', $meeting->is_recorded) ? 'checked' : '' }}
						class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
					>
				</div>
				<div class="ml-3">
					<label for="is_recorded" class="text-sm font-semibold text-gray-900">
						{{ org_trans('record_meeting') }}
					</label>
					<p class="text-xs text-gray-500">{{ org_trans('recording_available_after') }}</p>
				</div>
			</div>
		</div>

		<!-- Meeting Link -->
		<div class="mb-6">
			<label for="meeting_link" class="block text-sm font-semibold text-gray-900 mb-2">
				{{ org_trans('meeting_link') }}
			</label>
			<input
				type="url"
				id="meeting_link"
				name="meeting_link"
				value="{{ old('meeting_link', $meeting->meeting_link) }}"
				readonly
				class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600"
				placeholder="{{ org_trans('generated_automatically') }}"
			>
			<p class="mt-1 text-xs text-gray-500">{{ org_trans('link_cannot_be_modified') }}</p>
		</div>

		<!-- Recording URL (if completed) -->
		@if($meeting->status === 'completed')
		<div class="mb-6">
			<label for="recording_url" class="block text-sm font-semibold text-gray-900 mb-2">
				{{ org_trans('recording_url') }}
			</label>
			<input
				type="url"
				id="recording_url"
				name="recording_url"
				value="{{ old('recording_url', $meeting->recording_url) }}"
				class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('recording_url') border-red-500 @enderror"
				placeholder="https://..."
			>
			@error('recording_url')
			<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
			@enderror
		</div>

		<!-- AI Summary -->
		<div class="mb-6">
			<label for="ai_summary" class="block text-sm font-semibold text-gray-900 mb-2">
				{{ org_trans('ai_summary') }}
			</label>
			<textarea
				id="ai_summary"
				name="ai_summary"
				rows="6"
				class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('ai_summary') border-red-500 @enderror"
				placeholder="{{ org_trans('ai_summary_placeholder') }}"
			>{{ old('ai_summary', $meeting->ai_summary) }}</textarea>
			@error('ai_summary')
			<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
			@enderror
		</div>
		@endif

		<!-- Submit Buttons -->
		<div class="flex gap-4 pt-4 border-t border-gray-200">
			<button
				type="submit"
				class="flex-1 px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg transition-colors flex items-center justify-center"
			>
				<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
				</svg>
				{{ org_trans('save_changes') }}
			</button>
			<a
				href="{{ route('web.meetings.show', $meeting->id) }}"
				class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors text-center"
			>
				{{ org_trans('cancel') }}
			</a>
		</div>
	</form>
</div>
</x-app-layout>
