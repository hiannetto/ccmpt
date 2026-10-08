import { reactive } from 'vue';

// arquivo com variáveis do conteúdo home para no futuro criarmos uma página de personalização
export const siteSettings = reactive({
  colors: {
    primary: '#0a1128',
    secondary: '#D4AF37',
    textLight: '#ffffff',
    textGray: '#e2e8f0'
  },
  general: {
    siteName: 'Centro Cultural e Memorial Padre Tiago',
    logoUrl: '/logotipo.jpg',
    faviconUrl: '/favicon.ico',
  },
  hero: {
    title: 'Preservando a História, Transformando Vidas',
    subtitle: 'Conheça o legado do Padre Tiago e os projetos sociais mantidos pela instituição no coração de Muriaé.',
    backgroundImage: '/643572837_18105428236897999_3263018149113674385_n.jpg',
    cta1Text: 'Explore o Acervo',
    cta1Link: '/memorial',
    cta2Text: 'Nossos Projetos',
    cta2Link: '/pagina/projetos'
  },
  institutional: {
    title: 'Quem Somos',
    summary: 'O Centro Cultural e Memorial Padre Tiago é o coração vivo da comunidade em Muriaé. Dedicado à arte, educação e dignidade humana, perpetuamos o legado de Padre Tiago conectando história e transformação social.',
    ctaText: 'Leia mais sobre nossa história',
    ctaLink: '/pagina/instituicao'
  },
  projects: {
    title: 'Nossos Projetos e Oficinas',
    subtitle: 'Iniciativas que transformam vidas através da arte, esporte, cultura e acolhimento.',
    items: [
      { id: 1, title: 'Banda Marcial', icon: 'MusicNotes', description: 'Banda Marcial Bernadete Carneiro' },
      { id: 2, title: 'Folia de Reis', icon: 'Crown', description: 'Tradição e cultura popular' },
      { id: 3, title: 'Capoeira', icon: 'PersonArmsSpread', description: 'Esporte e expressão cultural' },
      { id: 4, title: 'Dança', icon: 'Sneaker', description: 'Aulas de dança para todas as idades' },
      { id: 5, title: 'Esporte', icon: 'SoccerBall', description: 'Escolinha de Futebol' },
      { id: 6, title: 'Sala de Leitura', icon: 'BookOpenText', description: 'Incentivo à leitura e educação' },
    ],
    ctaText: 'Ver todos os projetos e oficinas',
    ctaLink: '/pagina/projetos'
  },
  legacy: {
    title: 'O Legado de Padre Tiago',
    quote: '"O Revolucionário do Amor"',
    description: 'Conheça a história do criador do Projeto Pró-Moradia e líder espiritual que mudou a realidade da nossa região.',
    backgroundImage: 'https://images.unsplash.com/photo-1491841550275-ad7854e35ca6?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80',
    ctaText: 'Conheça a biografia completa',
    ctaLink: '/pagina/historia'
  },
  news: {
    title: 'Últimas Notícias e Acontecimentos',
  },
  gallery: {
    title: 'Galeria',
    ctaText: 'Acesse a galeria completa',
    ctaLink: '/galeria'
  },
  ctaFooter: {
    title: 'Faça parte da nossa história',
    subtitle: 'Venha nos visitar ou apoie nossa causa e ajude a transformar vidas.',
    ctaText: 'Fale Conosco',
    ctaLink: '/contato'
  },
  footer: {
    aboutTitle: 'Sobre o Centro Cultural',
    aboutText: 'O Centro Cultural e Memorial Padre Tiago é o coração vivo da comunidade em Muriaé. Dedicado à arte, educação e dignidade humana, perpetuamos o legado de Padre Tiago conectando história e transformação social.',
  }
});
