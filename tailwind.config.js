tailwind.config = {
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        primary: {"50":"#eff6ff","100":"#dbeafe","200":"#bfdbfe","300":"#93c5fd","400":"#60a5fa","500":"#3b82f6","600":"#2563eb","700":"#1d4ed8","800":"#1e40af","900":"#1e3a8a","950":"#172554"}
      },
      fontFamily: {
        'body': ['Inter', 'ui-sans-serif', 'system-ui'],
        'sans': ['Inter', 'ui-sans-serif', 'system-ui']
      },
      // Tambahan drop shadow yang lebih halus
      boxShadow: {
        'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.1)',
        'glow': '0 4px 20px 0px rgba(59, 130, 246, 0.5)', 
      }
    }
  }
}