@extends('layouts.marketing')

@section('title', "LocaTalk - Communication d'entreprise réinventée")

@push('styles')
<style>
	.gradient-text {
		background: linear-gradient(135deg, #df5526 0%, #fbbb2a 100%);
		-webkit-background-clip: text;
		-webkit-text-fill-color: transparent;
		background-clip: text;
		animation: gradientShift 3s ease infinite;
	}

	@keyframes gradientShift {
		0%, 100% { background-position: 0% 50%; }
		50% { background-position: 100% 50%; }
	}

	.hero-gradient {
		background: radial-gradient(ellipse at top, #1a1a2e 0%, #16213e 50%, #0f1419 100%);
		position: relative;
		overflow: hidden;
	}

	.hero-gradient::before,
	.hero-gradient::after {
		content: '';
		position: absolute;
		border-radius: 50%;
		filter: blur(80px);
	}

	.hero-gradient::before {
		width: 1200px;
		height: 1200px;
		background: radial-gradient(circle, rgba(223, 85, 38, 0.15) 0%, transparent 70%);
		top: -420px;
		right: -320px;
		animation: float 25s ease-in-out infinite;
	}

	.hero-gradient::after {
		width: 1000px;
		height: 1000px;
		background: radial-gradient(circle, rgba(251, 187, 42, 0.1) 0%, transparent 70%);
		bottom: -320px;
		left: -220px;
		animation: float 30s ease-in-out infinite reverse;
		filter: blur(100px);
	}

	@keyframes float {
		0%, 100% { transform: translate(0, 0) rotate(0deg) scale(1); }
		33% { transform: translate(100px, -50px) rotate(120deg) scale(1.1); }
		66% { transform: translate(-50px, 100px) rotate(240deg) scale(0.9); }
	}

	.particles {
		position: absolute;
		width: 100%;
		height: 100%;
		overflow: hidden;
	}

	.particle {
		position: absolute;
		background: linear-gradient(135deg, #df5526, #fbbb2a);
		border-radius: 50%;
		opacity: 0.1;
		animation: particleFloat linear infinite;
	}

	@keyframes particleFloat {
		0% {
			transform: translateY(100vh) scale(0);
			opacity: 0;
		}
		10% { opacity: 0.3; }
		90% { opacity: 0.1; }
		100% {
			transform: translateY(-100px) scale(1);
			opacity: 0;
		}
	}

	.glass-card {
		background: rgba(255, 255, 255, 0.02);
		backdrop-filter: blur(22px);
		border: 1px solid rgba(255, 255, 255, 0.05);
		position: relative;
		overflow: hidden;
	}

	.glass-card::before {
		content: '';
		position: absolute;
		top: 0;
		left: -100%;
		width: 100%;
		height: 100%;
		background: linear-gradient(90deg, transparent, rgba(223, 85, 38, 0.1), transparent);
		transition: left 0.5s;
	}

	.glass-card:hover::before { left: 100%; }

	.btn-gradient {
		background: linear-gradient(135deg, #df5526 0%, #fbbb2a 100%);
		background-size: 200% 200%;
		transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
		box-shadow: 0 15px 40px rgba(223, 85, 38, 0.4);
		position: relative;
		overflow: hidden;
	}

	.btn-gradient::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background: linear-gradient(135deg, #fbbb2a 0%, #df5526 100%);
		opacity: 0;
		transition: opacity 0.4s;
	}

	.btn-gradient:hover::before { opacity: 1; }

	.btn-gradient:hover {
		transform: translateY(-3px) scale(1.02);
		box-shadow: 0 20px 60px rgba(223, 85, 38, 0.6);
	}

	.btn-gradient span { position: relative; z-index: 1; }

	.feature-card {
		transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
		position: relative;
	}

	.feature-card::after {
		content: '';
		position: absolute;
		inset: 0;
		border-radius: 24px;
		padding: 1px;
		background: linear-gradient(135deg, rgba(223, 85, 38, 0.9), rgba(251, 187, 42, 0.9));
		-webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
		-webkit-mask-composite: xor;
		mask-composite: exclude;
		opacity: 0;
		transition: opacity 0.5s;
	}

	.feature-card:hover::after { opacity: 1; }

	.feature-card:hover { transform: translateY(-12px) scale(1.02); }

	.feature-card:hover .feature-icon { transform: scale(1.1) rotate(5deg); }

	.feature-icon {
		transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
	}

	nav { transition: all 0.3s; }

	nav.scrolled {
		background: rgba(0, 0, 0, 0.9);
		backdrop-filter: blur(16px);
		box-shadow: 0 4px 30px rgba(0, 0, 0, 0.7);
	}

	.stat-number {
		display: inline-block;
		transition: transform 0.3s;
	}

	.stat-card:hover .stat-number { transform: scale(1.1); }

	html { scroll-behavior: smooth; }

	.cursor-glow {
		position: fixed;
		width: 300px;
		height: 300px;
		background: radial-gradient(circle, rgba(223, 85, 38, 0.15) 0%, transparent 70%);
		border-radius: 50%;
		pointer-events: none;
		transform: translate(-50%, -50%);
		transition: opacity 0.3s;
		z-index: 9999;
		filter: blur(40px);
	}

	@keyframes pulse {
		0%, 100% { opacity: 1; transform: scale(1); }
		50% { opacity: 0.8; transform: scale(1.05); }
	}

	.pulse-animate { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }

	.badge-glow { box-shadow: 0 0 20px rgba(223, 85, 38, 0.3); }

	.reveal {
		opacity: 0;
		transform: translateY(30px);
		transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
	}

	.reveal.active {
		opacity: 1;
		transform: translateY(0);
	}

	.shimmer {
		position: relative;
		overflow: hidden;
	}

	.shimmer::after {
		content: '';
		position: absolute;
		top: 0;
		left: -100%;
		width: 100%;
		height: 100%;
		background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
		animation: shimmer 3s infinite;
	}

	@keyframes shimmer { 100% { left: 100%; } }
</style>
@endpush

@section('content')
<div class="cursor-glow" id="cursorGlow"></div>

<nav id="navbar" class="fixed top-0 left-0 right-0 z-50">
	<div class="container mx-auto px-6 py-4">
		<div class="flex items-center justify-between">
			<div class="flex items-center space-x-3 group cursor-pointer">
				<div class="w-11 h-11 rounded-xl flex items-center justify-center transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3 shimmer" style="background: linear-gradient(135deg, #df5526 0%, #fbbb2a 100%); box-shadow: 0 4px 20px rgba(223, 85, 38, 0.5);">
					<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
					</svg>
				</div>
				<span class="text-xl font-bold text-white">LocaTalk</span>
			</div>
			<div class="flex items-center space-x-4">
				<a href="/login" class="text-gray-300 hover:text-white transition-colors text-sm font-medium">
					Connexion
				</a>
				<a href="/register" class="btn-gradient px-6 py-2.5 rounded-xl text-white text-sm font-semibold">
					<span>Essai gratuit</span>
				</a>
			</div>
		</div>
	</div>
</nav>

<section class="hero-gradient relative min-h-screen flex items-center pt-20">
	<div class="particles">
		<div class="particle" style="width: 4px; height: 4px; left: 10%; animation-duration: 15s; animation-delay: 0s;"></div>
		<div class="particle" style="width: 6px; height: 6px; left: 20%; animation-duration: 20s; animation-delay: 2s;"></div>
		<div class="particle" style="width: 3px; height: 3px; left: 30%; animation-duration: 18s; animation-delay: 4s;"></div>
		<div class="particle" style="width: 5px; height: 5px; left: 40%; animation-duration: 22s; animation-delay: 1s;"></div>
		<div class="particle" style="width: 4px; height: 4px; left: 50%; animation-duration: 17s; animation-delay: 3s;"></div>
		<div class="particle" style="width: 6px; height: 6px; left: 60%; animation-duration: 19s; animation-delay: 5s;"></div>
		<div class="particle" style="width: 3px; height: 3px; left: 70%; animation-duration: 21s; animation-delay: 2s;"></div>
		<div class="particle" style="width: 5px; height: 5px; left: 80%; animation-duration: 16s; animation-delay: 4s;"></div>
		<div class="particle" style="width: 4px; height: 4px; left: 90%; animation-duration: 23s; animation-delay: 1s;"></div>
	</div>

	<div class="container mx-auto px-6 relative z-10">
		<div class="grid md:grid-cols-2 gap-16 items-center">
			<div class="text-left space-y-8 reveal">
				<div class="inline-block px-5 py-2.5 rounded-full glass-card badge-glow pulse-animate">
					<p class="text-sm font-semibold">
						<span class="gradient-text">✨ Nouveau</span>
						<span class="ml-2 text-gray-300">Appels vidéo HD intégrés</span>
					</p>
				</div>

				<h1 class="text-5xl md:text-7xl lg:text-8xl font-black text-white leading-[1.1] tracking-tight">
					Collaborez<br>
					<span class="gradient-text inline-block">sans limites</span>
				</h1>

				<p class="text-xl md:text-2xl text-gray-300 leading-relaxed max-w-xl">
					LocaTalk transforme la communication d'entreprise avec une plateforme tout-en-un :
					<span class="text-white font-semibold">messagerie, appels, projets</span> et bien plus.
				</p>

				<div class="flex flex-col sm:flex-row gap-4 pt-4">
					<a href="/register" class="btn-gradient px-10 py-5 rounded-xl text-white font-bold text-lg text-center group">
						<span class="flex items-center justify-center gap-2">
							Commencer gratuitement
							<svg class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
							</svg>
						</span>
					</a>
					<a href="#features" class="glass-card px-10 py-5 rounded-xl text-white font-bold text-lg text-center hover:bg-white/5 transition-all duration-300 border-2 border-white/10 hover:border-orange-500/30 group">
						<span class="flex items-center justify-center gap-2">
							Découvrir
							<svg class="w-5 h-5 transition-transform duration-300 group-hover:translate-y-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
							</svg>
						</span>
					</a>
				</div>

				<div class="grid grid-cols-3 gap-8 pt-12">
					<div class="text-center stat-card group cursor-pointer">
						<div class="text-4xl md:text-5xl font-black gradient-text stat-number">10K+</div>
						<div class="text-sm text-gray-400 mt-2 group-hover:text-white transition-colors">{{ org_trans('active') }} {{ org_trans('users') }}</div>
					</div>
					<div class="text-center stat-card group cursor-pointer">
						<div class="text-4xl md:text-5xl font-black gradient-text stat-number">99.9%</div>
						<div class="text-sm text-gray-400 mt-2 group-hover:text-white transition-colors">Disponibilité</div>
					</div>
					<div class="text-center stat-card group cursor-pointer">
						<div class="text-4xl md:text-5xl font-black gradient-text stat-number">24/7</div>
						<div class="text-sm text-gray-400 mt-2 group-hover:text-white transition-colors">Support dédié</div>
					</div>
				</div>
			</div>

			<div class="relative reveal hidden md:block" style="transition-delay: 0.2s;">
				<div class="relative">
					<div class="glass-card rounded-3xl p-8 border border-white/10 transform hover:scale-105 transition-all duration-500">
						<div class="space-y-6">
							<div class="flex items-center gap-4">
								<div class="w-12 h-12 rounded-full bg-gradient-to-br from-orange-500 to-yellow-400"></div>
								<div class="flex-1">
									<div class="h-3 bg-white/10 rounded w-32 mb-2"></div>
									<div class="h-2 bg-white/5 rounded w-24"></div>
								</div>
							</div>

							<div class="space-y-4">
								<div class="glass-card rounded-2xl p-4 border border-orange-500/20">
									<div class="h-2 bg-white/20 rounded w-full mb-2"></div>
									<div class="h-2 bg-white/10 rounded w-3/4"></div>
								</div>
								<div class="glass-card rounded-2xl p-4 border border-yellow-500/20 ml-8">
									<div class="h-2 bg-white/20 rounded w-full mb-2"></div>
									<div class="h-2 bg-white/10 rounded w-2/3"></div>
								</div>
							</div>
						</div>
					</div>

					<div class="absolute -top-6 -right-6 w-24 h-24 rounded-2xl bg-gradient-to-br from-orange-500/20 to-yellow-400/20 backdrop-blur-xl border border-orange-500/30 pulse-animate"></div>
					<div class="absolute -bottom-4 -left-4 w-20 h-20 rounded-full bg-gradient-to-br from-yellow-400/20 to-orange-500/20 backdrop-blur-xl border border-yellow-500/30 pulse-animate" style="animation-delay: 1s;"></div>
				</div>
			</div>
		</div>
	</div>
</section>

<section id="features" class="py-32 bg-gradient-to-b from-gray-900 via-gray-800 to-gray-900 relative">
	<div class="container mx-auto px-6">
		<div class="text-center mb-24 reveal">
			<div class="inline-block px-6 py-2 rounded-full glass-card mb-6">
				<span class="text-sm font-semibold gradient-text">FONCTIONNALITÉS</span>
			</div>
			<h2 class="text-4xl md:text-6xl lg:text-7xl font-black text-white mb-6 tracking-tight">
				Tout ce dont votre<br>équipe a <span class="gradient-text">besoin</span>
			</h2>
			<p class="text-xl md:text-2xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
				Une plateforme complète pour collaborer efficacement, où que vous soyez
			</p>
		</div>

		<div class="grid md:grid-cols-3 gap-8 lg:gap-10">
			<div class="feature-card glass-card rounded-3xl p-10 border border-white/10 reveal" style="transition-delay: 0.1s;">
				<div class="feature-icon w-16 h-16 rounded-2xl flex items-center justify-center mb-8" style="background: linear-gradient(135deg, rgba(223, 85, 38, 0.1) 0%, rgba(251, 187, 42, 0.1) 100%); border: 2px solid rgba(223, 85, 38, 0.3); box-shadow: 0 8px 24px rgba(223, 85, 38, 0.2);">
					<svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
					</svg>
				</div>
				<h3 class="text-2xl md:text-3xl font-bold text-white mb-4">Messagerie instantanée</h3>
				<p class="text-gray-400 leading-relaxed text-lg mb-6">
					Conversations individuelles ou de groupe avec partage de fichiers, emojis et recherche avancée.
				</p>
				<a href="#" class="inline-flex items-center text-orange-500 font-semibold hover:gap-2 transition-all duration-300 group">
					En savoir plus
					<svg class="w-4 h-4 ml-1 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
					</svg>
				</a>
			</div>

			<div class="feature-card glass-card rounded-3xl p-10 border border-white/10 reveal" style="transition-delay: 0.2s;">
				<div class="feature-icon w-16 h-16 rounded-2xl flex items-center justify-center mb-8" style="background: linear-gradient(135deg, rgba(223, 85, 38, 0.1) 0%, rgba(251, 187, 42, 0.1) 100%); border: 2px solid rgba(223, 85, 38, 0.3); box-shadow: 0 8px 24px rgba(223, 85, 38, 0.2);">
					<svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
					</svg>
				</div>
				<h3 class="text-2xl md:text-3xl font-bold text-white mb-4">Appels vidéo HD</h3>
				<p class="text-gray-400 leading-relaxed text-lg mb-6">
					Visioconférences cristallines avec partage d'écran, enregistrement et jusqu'à 100 participants.
				</p>
				<a href="#" class="inline-flex items-center text-orange-500 font-semibold hover:gap-2 transition-all duration-300 group">
					En savoir plus
					<svg class="w-4 h-4 ml-1 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
					</svg>
				</a>
			</div>

			<div class="feature-card glass-card rounded-3xl p-10 border border-white/10 reveal" style="transition-delay: 0.3s;">
				<div class="feature-icon w-16 h-16 rounded-2xl flex items-center justify-center mb-8" style="background: linear-gradient(135deg, rgba(223, 85, 38, 0.1) 0%, rgba(251, 187, 42, 0.1) 100%); border: 2px solid rgba(223, 85, 38, 0.3); box-shadow: 0 8px 24px rgba(223, 85, 38, 0.2);">
					<svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
					</svg>
				</div>
				<h3 class="text-2xl md:text-3xl font-bold text-white mb-4">Gestion de projets</h3>
				<p class="text-gray-400 leading-relaxed text-lg mb-6">
					Tableaux Kanban intelligents, tâches, deadlines et suivi de progression en temps réel.
				</p>
				<a href="#" class="inline-flex items-center text-orange-500 font-semibold hover:gap-2 transition-all duration-300 group">
					En savoir plus
					<svg class="w-4 h-4 ml-1 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
					</svg>
				</a>
			</div>
		</div>
	</div>
</section>

<section class="py-40 relative overflow-hidden bg-white">
	<div class="absolute inset-0">
		<div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-orange-500/5 rounded-full blur-3xl"></div>
	</div>

	<div class="container mx-auto px-6 relative z-10">
		<div class="rounded-[2.5rem] p-12 md:p-24 text-center border-2 border-gray-100 reveal bg-white shadow-2xl">
			<div class="inline-block px-6 py-2 rounded-full bg-orange-50 border border-orange-100 mb-8">
				<span class="text-sm font-semibold gradient-text">🚀 COMMENCEZ MAINTENANT</span>
			</div>

			<h2 class="text-4xl md:text-6xl lg:text-7xl font-black text-gray-900 mb-8 leading-tight">
				Prêt à transformer votre<br>
				<span class="gradient-text">communication d'entreprise ?</span>
			</h2>

			<p class="text-xl md:text-2xl text-gray-600 mb-12 max-w-3xl mx-auto leading-relaxed">
				Rejoignez des milliers d'équipes qui utilisent LocaTalk pour collaborer efficacement
			</p>

			<div class="flex flex-col sm:flex-row gap-6 justify-center mb-10">
				<a href="/register" class="btn-gradient px-12 py-5 rounded-xl text-white font-bold text-lg group">
					<span class="flex items-center justify-center gap-3">
						Commencer gratuitement
						<svg class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
						</svg>
					</span>
				</a>
				<a href="/login" class="px-12 py-5 rounded-xl text-gray-700 font-bold text-lg hover:bg-gray-50 transition-all duration-300 border-2 border-gray-200 hover:border-orange-500">
					Se connecter
				</a>
			</div>

			<div class="flex items-center justify-center gap-6 text-gray-600">
				<div class="flex items-center gap-2">
					<svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
						<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
					</svg>
					<span class="text-sm font-medium">Aucune carte requise</span>
				</div>
				<div class="w-1 h-1 rounded-full bg-gray-300"></div>
				<div class="flex items-center gap-2">
					<svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
						<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
					</svg>
					<span class="text-sm font-medium">14 jours d'essai gratuit</span>
				</div>
				<div class="w-1 h-1 rounded-full bg-gray-300"></div>
				<div class="flex items-center gap-2">
					<svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
						<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
					</svg>
					<span class="text-sm font-medium">Annulation à tout moment</span>
				</div>
			</div>
		</div>
	</div>
</section>

<footer class="py-20 relative border-t border-white/10 bg-black">
	<div class="container mx-auto px-6">
		<div class="grid md:grid-cols-4 gap-12 mb-16">
			<div class="md:col-span-2 space-y-6">
				<div class="flex items-center space-x-3">
					<div class="w-12 h-12 rounded-2xl flex items-center justify-center shimmer" style="background: linear-gradient(135deg, #df5526 0%, #fbbb2a 100%); box-shadow: 0 4px 20px rgba(223, 85, 38, 0.5);">
						<svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
						</svg>
					</div>
					<span class="text-2xl font-black text-white">LocaTalk</span>
				</div>
				<p class="text-gray-400 text-lg leading-relaxed max-w-md">
					La plateforme de communication d'entreprise nouvelle génération. Propulsée par l'IA, conçue pour l'excellence.
				</p>
				<div class="flex items-center gap-4">
					<a href="#" class="w-10 h-10 rounded-xl glass-card flex items-center justify-center text-gray-400 hover:text-white hover:border-orange-500/30 transition-all duration-300">
						<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
					</a>
					<a href="#" class="w-10 h-10 rounded-xl glass-card flex items-center justify-center text-gray-400 hover:text-white hover:border-orange-500/30 transition-all duration-300">
						<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
					</a>
					<a href="#" class="w-10 h-10 rounded-xl glass-card flex items-center justify-center text-gray-400 hover:text-white hover:border-orange-500/30 transition-all duration-300">
						<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
					</a>
				</div>
			</div>

			<div>
				<h4 class="text-white font-bold text-lg mb-6">Produit</h4>
				<ul class="space-y-4">
					<li><a href="#" class="text-gray-400 hover:text-white transition-colors">Fonctionnalités</a></li>
					<li><a href="#" class="text-gray-400 hover:text-white transition-colors">Tarifs</a></li>
					<li><a href="#" class="text-gray-400 hover:text-white transition-colors">Sécurité</a></li>
					<li><a href="#" class="text-gray-400 hover:text-white transition-colors">Intégrations</a></li>
				</ul>
			</div>

			<div>
				<h4 class="text-white font-bold text-lg mb-6">Entreprise</h4>
				<ul class="space-y-4">
					<li><a href="#" class="text-gray-400 hover:text-white transition-colors">À propos</a></li>
					<li><a href="#" class="text-gray-400 hover:text-white transition-colors">Blog</a></li>
					<li><a href="#" class="text-gray-400 hover:text-white transition-colors">Carrières</a></li>
					<li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contact</a></li>
				</ul>
			</div>
		</div>

		<div class="pt-8 border-t border-white/10 flex flex-col md:flex-row items-center justify-between gap-6">
			<p class="text-gray-400 text-sm">© 2025 LocaTalk. Tous droits réservés.</p>
			<div class="flex items-center gap-8 text-sm">
				<a href="#" class="text-gray-400 hover:text-white transition-colors">Confidentialité</a>
				<a href="#" class="text-gray-400 hover:text-white transition-colors">Conditions</a>
				<a href="#" class="text-gray-400 hover:text-white transition-colors">Cookies</a>
			</div>
		</div>
	</div>
</footer>
@endsection

@push('scripts')
<script>
	const cursorGlow = document.getElementById('cursorGlow');
	document.addEventListener('mousemove', (event) => {
		if (cursorGlow) {
			cursorGlow.style.left = event.clientX + 'px';
			cursorGlow.style.top = event.clientY + 'px';
		}
	});

	const navbar = document.getElementById('navbar');
	window.addEventListener('scroll', () => {
		if (navbar) {
			if (window.scrollY > 100) {
				navbar.classList.add('scrolled');
			} else {
				navbar.classList.remove('scrolled');
			}
		}
	});

	const revealOnScroll = () => {
		document.querySelectorAll('.reveal').forEach((element) => {
			const elementTop = element.getBoundingClientRect().top;
			const windowHeight = window.innerHeight;
			if (elementTop < windowHeight - 100) {
				element.classList.add('active');
			}
		});
	};

	window.addEventListener('scroll', revealOnScroll);
	revealOnScroll();

	document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
		anchor.addEventListener('click', (event) => {
			const target = document.querySelector(anchor.getAttribute('href'));
			if (target) {
				event.preventDefault();
				target.scrollIntoView({ behavior: 'smooth', block: 'start' });
			}
		});
	});
</script>
@endpush
