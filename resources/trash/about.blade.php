<!-- resources/views/about.blade.php -->
<x-app-layout title="Sobre Nós - Laboratório Lamarck">

    <x-breadcrumb :links="[
    ['url' => '', 'label' => 'Sobre Nós']]" />

    <!-- Hero Section Modernizado -->
    <section class="relative py-24 bg-gradient-to-br from-primary/10 to-primary-light/10 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-t from-background/20 via-transparent to-transparent"></div>
        <x-container class="relative">
            <div class="max-w-4xl mx-auto text-center" data-aos="fade-up">
                <div class="inline-flex items-center bg-primary/10 px-4 py-2 rounded-full mb-6">
                    <svg class="w-5 h-5 text-primary mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <span class="text-primary font-medium">Cuidando da sua saúde há mais de 20 anos</span>
                </div>
                <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
                    Compromisso com a <span class="text-primary">Excelência</span>
                </h1>
                <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                    Somos referência em diagnósticos laboratoriais, combinando tecnologia avançada com atendimento humanizado para cuidar melhor da sua saúde.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <x-link href="/exames" class="bg-primary text-white hover:bg-primary-dark px-6 py-3 rounded-lg font-medium transition-colors inline-flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Ver Todos os Exames
                    </x-link>
                    <x-link href="/orcamento" variant="outline" class="border-2 border-primary text-primary hover:bg-primary hover:text-white px-6 py-3 rounded-lg font-medium transition-colors inline-flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                        Solicitar Orçamento
                    </x-link>
                </div>
            </div>
        </x-container>
    </section>

    <!-- Estatísticas -->
    <section class="py-16 bg-white">
        <x-container>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center" data-aos="zoom-in">
                    <div class="text-4xl font-bold text-primary mb-2">20+</div>
                    <div class="text-gray-600">Anos de Experiência</div>
                </div>
                <div class="text-center" data-aos="zoom-in" data-aos-delay="100">
                    <div class="text-4xl font-bold text-primary mb-2">500+</div>
                    <div class="text-gray-600">Tipos de Exames</div>
                </div>
                <div class="text-center" data-aos="zoom-in" data-aos-delay="200">
                    <div class="text-4xl font-bold text-primary mb-2">98%</div>
                    <div class="text-gray-600">Satisfação dos Clientes</div>
                </div>
                <div class="text-center" data-aos="zoom-in" data-aos-delay="300">
                    <div class="text-4xl font-bold text-primary mb-2">24h</div>
                    <div class="text-gray-600">Resultados Rápidos</div>
                </div>
            </div>
        </x-container>
    </section>

    <!-- Nossa História -->
    <section class="py-20 bg-gray-50">
        <x-container>
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Nossa História</h2>
                    <div class="space-y-4 text-gray-600">
                        <p>
                            Fundado em 2000, o Laboratório Lamarck nasceu com a missão de democratizar o acesso a diagnósticos de qualidade. Ao longo de mais de duas décadas, crescemos e nos consolidamos como referência em análises clínicas na região.
                        </p>
                        <p>
                            Nossa trajetória é marcada por investimentos constantes em tecnologia, capacitação da equipe e melhoria contínua dos processos, sempre priorizando a precisão dos resultados e a satisfação dos nossos pacientes.
                        </p>
                        <p>
                            Hoje, somos reconhecidos pela qualidade técnica, agilidade na entrega de resultados e pelo atendimento humanizado que oferecemos a cada cliente.
                        </p>
                    </div>
                </div>
                <div data-aos="fade-left">
                    <div class="bg-gradient-to-br from-primary/10 to-primary-light/10 rounded-2xl p-8">
                        <img src="/img/laboratorio-moderno.jpg" alt="Laboratório Lamarck"
                            class="w-full h-64 object-cover rounded-lg shadow-lg mb-6">
                        <div class="text-center">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Tecnologia de Ponta</h3>
                            <p class="text-gray-600">Equipamentos modernos para resultados precisos</p>
                        </div>
                    </div>
                </div>
            </div>
        </x-container>
    </section>

    <!-- Missão, Visão e Valores -->
    <section class="py-20 bg-white">
        <x-container>
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Nossos Pilares</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Os valores que nos guiam em nossa missão de cuidar da sua saúde
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center p-8 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200" data-aos="zoom-in">
                    <div class="bg-blue-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4 text-blue-900">Missão</h3>
                    <p class="text-blue-700">
                        Fornecer diagnósticos laboratoriais precisos e confiáveis, contribuindo para a promoção da saúde e qualidade de vida de nossos pacientes.
                    </p>
                </div>

                <div class="text-center p-8 rounded-2xl bg-gradient-to-br from-green-50 to-green-100 border border-green-200" data-aos="zoom-in" data-aos-delay="100">
                    <div class="bg-green-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4 text-green-900">Visão</h3>
                    <p class="text-green-700">
                        Ser reconhecido como o laboratório de referência na região, destacando-se pela excelência técnica e atendimento humanizado.
                    </p>
                </div>

                <div class="text-center p-8 rounded-2xl bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200" data-aos="zoom-in" data-aos-delay="200">
                    <div class="bg-purple-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4 text-purple-900">Valores</h3>
                    <p class="text-purple-700">
                        Ética, qualidade, responsabilidade social, inovação tecnológica e compromisso com a excelência em tudo que fazemos.
                    </p>
                </div>
            </div>
        </x-container>
    </section>

    <!-- Equipe -->
    <section class="py-20 bg-gray-50">
        <x-container>
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Nossa Equipe</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Profissionais qualificados e comprometidos com a sua saúde
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center bg-white rounded-2xl p-8 shadow-lg" data-aos="zoom-in">
                    <div class="w-32 h-32 mx-auto mb-6 rounded-full bg-gradient-to-br from-primary to-primary-dark overflow-hidden">
                        <img src="/img/team1.jpg" alt="Dr. Jocléssio de Jesus Leite" class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Dr. Jocléssio de Jesus Leite</h3>
                    <p class="text-primary font-medium mb-2">Diretor Técnico</p>
                    <p class="text-sm text-gray-600">CRBM/MG 5497</p>
                    <p class="text-sm text-gray-500 mt-2">Biomédico com mais de 15 anos de experiência em análises clínicas</p>
                </div>

                <div class="text-center bg-white rounded-2xl p-8 shadow-lg" data-aos="zoom-in" data-aos-delay="100">
                    <div class="w-32 h-32 mx-auto mb-6 rounded-full bg-gradient-to-br from-green-400 to-green-600 overflow-hidden flex items-center justify-center">
                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Equipe Técnica</h3>
                    <p class="text-green-600 font-medium mb-2">Analistas Clínicos</p>
                    <p class="text-sm text-gray-600">Certificados e especializados</p>
                    <p class="text-sm text-gray-500 mt-2">Profissionais capacitados para garantir a precisão dos resultados</p>
                </div>

                <div class="text-center bg-white rounded-2xl p-8 shadow-lg" data-aos="zoom-in" data-aos-delay="200">
                    <div class="w-32 h-32 mx-auto mb-6 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 overflow-hidden flex items-center justify-center">
                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Atendimento</h3>
                    <p class="text-blue-600 font-medium mb-2">Equipe de Recepção</p>
                    <p class="text-sm text-gray-600">Atendimento humanizado</p>
                    <p class="text-sm text-gray-500 mt-2">Profissionais treinados para oferecer o melhor atendimento</p>
                </div>
            </div>
        </x-container>
    </section>

    <!-- Certificações e Qualidade -->
    <section class="py-20 bg-white">
        <x-container>
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Qualidade e Certificações</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Compromisso com os mais altos padrões de qualidade
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right">
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="bg-green-100 p-3 rounded-lg">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold mb-2">Controle de Qualidade Rigoroso</h3>
                                <p class="text-gray-600">Calibração diária dos equipamentos e controles internos em todos os exames.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="bg-blue-100 p-3 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold mb-2">Tecnologia Avançada</h3>
                                <p class="text-gray-600">Equipamentos de última geração para resultados rápidos e precisos.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="bg-purple-100 p-3 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold mb-2">Certificações</h3>
                                <p class="text-gray-600">Seguimos as normas da ANVISA e mantemos certificações atualizadas.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div data-aos="fade-left">
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-8">
                        <div class="text-center mb-8">
                            <div class="bg-primary w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Certificado de Qualidade</h3>
                            <p class="text-gray-600">Atendemos aos mais rigorosos padrões de qualidade em análises clínicas</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <div class="text-2xl font-bold text-primary">100%</div>
                                <div class="text-sm text-gray-600">Conformidade</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-primary">ISO</div>
                                <div class="text-sm text-gray-600">Padrões</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-container>
    </section>

    <!-- CTA Final -->
    <section class="py-20 bg-gradient-to-br from-primary to-primary-dark text-white">
        <x-container class="text-center">
            <div data-aos="fade-up">
                <h2 class="text-4xl font-bold mb-6">Pronto para Cuidar da Sua Saúde?</h2>
                <p class="text-lg mb-8 opacity-90 max-w-2xl mx-auto">
                    Agende seus exames com praticidade e tenha a tranquilidade de resultados precisos e confiáveis
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <x-link href="/orcamento" class="bg-white text-primary hover:bg-gray-100 px-8 py-3 rounded-lg font-semibold transition-colors inline-flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 9l6 6 6-6m-6 6v-6m0 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v8z" />
                        </svg>
                        Agendar Exame
                    </x-link>
                    <x-link href="/chat" class="border-2 border-white text-white hover:bg-white hover:text-primary px-8 py-3 rounded-lg font-semibold transition-colors inline-flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.991 8.991 0 01-4.255-1.165L3 21l2.165-5.59A8.989 8.989 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z" />
                        </svg>
                        Chat com IA
                    </x-link>
                </div>
            </div>
        </x-container>
    </section>
</x-app-layout>