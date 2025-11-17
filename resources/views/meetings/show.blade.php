<x-app-layout>
<div class="p-4 sm:p-6 lg:p-8">
	<!-- Back Button -->
	<div class="mb-6">
		<a href="{{ route('web.meetings') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-orange-600 transition-colors">
			<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
			</svg>
			{{ org_trans('back_to_meetings') }}
		</a>
	</div>

	<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
		<!-- Main Content -->
		<div class="lg:col-span-2 space-y-6">
			<!-- Header Card -->
			<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
				<div class="flex items-start justify-between mb-4">
					<div class="flex-1">
						<h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">{{ $meeting->title }}</h1>

						<!-- Status Badge -->
						@if($meeting->status === 'scheduled')
						<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
							<svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
								<circle cx="10" cy="10" r="3"></circle>
							</svg>
							{{ org_trans('scheduled') }}
						</span>
						@elseif($meeting->status === 'ongoing')
						<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-orange-100 text-orange-800 animate-pulse">
							<svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
								<circle cx="10" cy="10" r="3"></circle>
							</svg>
							{{ org_trans('ongoing') }}
						</span>
						@elseif($meeting->status === 'completed')
						<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-800">
							<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
							</svg>
							{{ org_trans('completed') }}
						</span>
						@else
						<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">
							{{ org_trans('cancelled') }}
						</span>
						@endif
					</div>

					<!-- Actions Dropdown -->
					@if(auth()->user()->id === $meeting->created_by || auth()->user()->can('manage-users'))
					<div class="relative" x-data="{ open: false }">
						<button @click="open = !open" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
							<svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
							</svg>
						</button>
						<div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10">
							<a href="{{ route('web.meetings.edit', $meeting->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
								<svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
								</svg>
								{{ org_trans('edit_meeting') }}
							</a>
							<form action="{{ route('web.meetings.destroy', $meeting->id) }}" method="POST">
								@csrf
								@method('DELETE')
								<button type="submit" onclick="return confirm('{{ org_trans('confirm_delete_meeting') }}')" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
									<svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
									</svg>
									{{ org_trans('delete') }}
								</button>
							</form>
						</div>
					</div>
					@endif
				</div>

				<!-- Description -->
				@if($meeting->description)
				<div class="mt-6">
					<h3 class="text-sm font-semibold text-gray-900 mb-2">{{ org_trans('description') }}</h3>
					<p class="text-gray-700 whitespace-pre-wrap">{{ $meeting->description }}</p>
				</div>
				@endif

				<!-- Meeting Access Info -->
				@if($meeting->meeting_link && in_array($meeting->status, ['scheduled', 'ongoing']))
				<div class="mt-6 p-4 bg-orange-50 border border-orange-200 rounded-lg">
					<div class="flex items-center justify-between">
						<div class="flex-1">
							<p class="text-sm font-semibold text-gray-900 mb-1">{{ org_trans('meeting_code') }}</p>
							<p class="text-sm text-gray-600 mb-2">Code unique: <span class="font-mono font-semibold text-orange-700">{{ substr($meeting->meeting_link, 0, 8) }}...</span></p>
							<p class="text-xs text-gray-500">{{ org_trans('meeting_access_info') }}</p>
						</div>
						<button onclick="navigator.clipboard.writeText('{{ route('web.meetings.show', $meeting->id) }}'); alert('Lien copié !');" class="ml-4 px-3 py-2 bg-white border border-orange-300 text-orange-700 rounded-lg hover:bg-orange-50 transition-colors" title="Copier le lien de la page">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
							</svg>
						</button>
					</div>
				</div>
				@endif

				<!-- Action Buttons -->
				<div class="mt-6 flex flex-wrap gap-3">
					@if($meeting->status === 'scheduled' && auth()->user()->id === $meeting->created_by)
					<form action="{{ route('web.meetings.start', $meeting->id) }}" method="POST" class="inline">
						@csrf
						<button type="submit" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors flex items-center">
							<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
							</svg>
							{{ org_trans('start_meeting') }}
						</button>
					</form>
					@endif

					@if($meeting->status === 'ongoing')
					<form action="{{ route('web.meetings.join', $meeting->id) }}" method="POST" class="inline">
						@csrf
						<button type="submit" class="px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white font-semibold rounded-lg transition-colors flex items-center animate-pulse">
							<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
							</svg>
							Rejoindre maintenant
						</button>
					</form>

					@if(auth()->user()->id === $meeting->created_by)
					<form action="{{ route('web.meetings.end', $meeting->id) }}" method="POST" class="inline">
						@csrf
						<button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors flex items-center">
							<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
							</svg>
							{{ org_trans('end_meeting') }}
						</button>
					</form>
					@endif
					@endif
				</div>
			</div>

			<!-- Recording & AI Summary -->
			@if($meeting->status === 'completed')
			<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
				<h3 class="text-lg font-semibold text-gray-900 mb-4">Enregistrement et Résumé</h3>

				@if($meeting->recording_url)
				<div class="mb-6">
					<h4 class="text-sm font-semibold text-gray-900 mb-2">📹 Enregistrement vidéo</h4>
					<a href="{{ $meeting->recording_url }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
						<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
						</svg>
						Regarder l'enregistrement
					</a>
				</div>
				@endif

				@if($meeting->ai_summary)
				<div>
					<h4 class="text-sm font-semibold text-gray-900 mb-2">🤖 Résumé IA</h4>
					<div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
						<p class="text-gray-700 whitespace-pre-wrap">{{ $meeting->ai_summary }}</p>
					</div>
				</div>
				@endif
			</div>
			@endif
		</div>

		<!-- Sidebar -->
		<div class="space-y-6">
			<!-- Meeting Info -->
			<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
				<h3 class="text-lg font-semibold text-gray-900 mb-4">Informations</h3>

				<div class="space-y-4">
					<!-- Date & Time -->
					<div class="flex items-start">
						<svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
						</svg>
						<div>
							<p class="text-sm font-semibold text-gray-900">{{ $meeting->start_time->format('d/m/Y') }}</p>
							<p class="text-sm text-gray-600">{{ $meeting->start_time->format('H:i') }} - {{ $meeting->end_time ? $meeting->end_time->format('H:i') : 'N/A' }}</p>
							@if($meeting->duration)
							<p class="text-xs text-gray-500 mt-1">Durée: {{ $meeting->duration }} min</p>
							@endif
						</div>
					</div>

					<!-- Creator -->
					<div class="flex items-start">
						<svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
						</svg>
						<div>
							<p class="text-sm font-semibold text-gray-900">{{ org_trans('organizer') }}</p>
							<p class="text-sm text-gray-600">{{ $meeting->creator->name }}</p>
							<p class="text-xs text-gray-500">{{ $meeting->creator->email }}</p>
						</div>
					</div>

					<!-- Recording -->
					<div class="flex items-start">
						<svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
						</svg>
						<div>
							<p class="text-sm font-semibold text-gray-900">{{ org_trans('recording') }}</p>
							<p class="text-sm text-gray-600">{{ $meeting->is_recorded ? org_trans('enabled') : org_trans('disabled') }}</p>
						</div>
					</div>
				</div>
			</div>

			<!-- Participants -->
			<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
				<h3 class="text-lg font-semibold text-gray-900 mb-4">
					{{ org_trans('participants') }} ({{ $meeting->participants->count() }})
				</h3>

				<div class="space-y-3">
					@forelse($meeting->participants as $participant)
					<div class="flex items-center justify-between">
						<div class="flex items-center">
							<div class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-500 to-yellow-500 flex items-center justify-center text-white font-semibold text-sm">
								{{ strtoupper(substr($participant->name, 0, 2)) }}
							</div>
							<div class="ml-3">
								<p class="text-sm font-semibold text-gray-900">{{ $participant->name }}</p>
								<p class="text-xs text-gray-500">{{ $participant->email }}</p>
							</div>
						</div>
						@if($participant->pivot->joined_at)
						<span class="text-xs text-green-600 font-semibold">{{ org_trans('present') }}</span>
						@endif
					</div>
					@empty
					<p class="text-sm text-gray-500 text-center py-4">{{ org_trans('no_participants') }}</p>
					@endforelse
				</div>
			</div>
		</div>
	</div>
</div>
</x-app-layout>
