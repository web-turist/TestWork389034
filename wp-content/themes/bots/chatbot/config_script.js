console.log('my_script');
// конфигурация чат-бота
const configChatbot = {};
// CSS-селектор кнопки, посредством которой будем вызывать окно диалога с чат-ботом
configChatbot.btn = '.chatbot__btn';
// ключ для хранения отпечатка браузера
configChatbot.key = 'fingerprint';
// реплики чат-бота
configChatbot.replicas = {
bot: {
0: { content: ['Привет!', 'Я Динеш - бот поддержки сайта <a href="https://thisisbot.ru" target="_blank">thisisbot.ru</a>'], human: [0, 1, 2] },
1: { content: 'Как я могу к вам обращаться?', human: [3] },
2: { content: 'Чуваааааак! Как звать тебя?', human: [3] },
3: { content: '{{name}}, что Вас интересует?', human: [4, 5] },
4: { content: '{{name}}, ну давай рассказывай, зачем пожаловал? Бота поди ищешь себе классного?', human: [6] },
5: { content: "{{name}}, какой у Вас вопрос?", human: [7] },
6: { content: '{{name}}, мы получили Ваш вопрос! Скажите, как с Вами удобнее будет связаться?', human: [8, 9] },
7: { content: '{{name}}, укажите пожалуйста ваш телефон', human: [10] },
8: { content: '{{name}}, укажите пожалуйста ваш Email ниже', human: [10] },
9: { content: 'Готово! {{name}}, мы свяжемся с вами в ближайшее время по {{contact}}. Всего хорошего!', human: [6] },
},
human: {
0: { content: 'Салют!', bot: 1 },
1: { content: 'Здарова чувак!', bot: 2 },
2: { content: 'Привет, Динеш.', bot: 1 },
3: { content: '', bot: 3, name: 'name' },
4: { content: 'Меня интересует консультация по покупке бота', bot: 5 },
5: { content: 'Хочу оставить запрос на разработку бота', bot: 5 },
6: { content: 'В начало', bot: 0 },
7: { content: '', bot: 6, name: '' },
8: { content: 'по телефону', bot: 7 },
9: { content: 'по email', bot: 8 },
10: { content: '', bot: 9, name: 'contact' },
11: { content: 'Спасибо. Всего доброго)'}
}
}
// корневой элемент
configChatbot.root = SimpleChatbot.createTemplate();
// console.log(SimpleChatbot);
// URL chatbot.php
configChatbot.url = '/bots/wp-content/themes/bots/chatbot/chatbot.php';
// создание SimpleChatbot
let chatbot = new SimpleChatbot(configChatbot);
// при клике по кнопке configChatbot.btn
document.querySelector(configChatbot.btn).onclick = function (e) {
this.classList.add('d-none');
const $tooltip = this.querySelector('.chatbot__tooltip');
if ($tooltip) {
$tooltip.classList.add('d-none');
}
configChatbot.root.classList.toggle('chatbot_hidden');
chatbot.init();
};

// добавление ключа для хранения отпечатка браузера в LocalStorage
let fingerprint = localStorage.getItem(configChatbot.key);
if (!fingerprint) {
Fingerprint2.get(function (components) {
fingerprint = Fingerprint2.x64hash128(components.map(function (pair) {
  return pair.value
}).join(), 31)
localStorage.setItem(configChatbot.key, fingerprint)
});
}

// подсказка для кнопки
const $btn = document.querySelector(configChatbot.btn);
$btn.addEventListener('mouseover', function (e) {
const $tooltip = $btn.querySelector('.chatbot__tooltip');
if (!$tooltip.classList.contains('chatbot__tooltip_show')) {
$tooltip.classList.remove('d-none');
setTimeout(function () {
  $tooltip.classList.add('chatbot__tooltip_show');
}, 0);
}
});
$btn.addEventListener('mouseout', function (e) {
const $tooltip = $btn.querySelector('.chatbot__tooltip');
if ($tooltip.classList.contains('chatbot__tooltip_show')) {
$tooltip.classList.remove('chatbot__tooltip_show');
setTimeout(function () {
  $tooltip.classList.add('d-none');
}, 200);
}
});

setTimeout(function () {
const tooltip = document.querySelector('.chatbot__tooltip');
tooltip.classList.add('chatbot__tooltip_show');
setTimeout(function () {
tooltip.classList.remove('chatbot__tooltip_show');
}, 10000)
}, 10000);
