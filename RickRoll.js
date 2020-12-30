/**
 * JavaScript called in Group1 and Group2 website to change their website appearance.
 */

document.body.style.backgroundImage = "url('https://media4.giphy.com/media/Ju7l5y9osyymQ/giphy.gif')";
document.body.style.backgroundRepeat = 'repeat';
document.title = "Group 4 Is The Best";
const iframe = document.createElement('iframe');
iframe.src = "https://www.youtube.com/embed/oHg5SJYRHA0?autoplay=1&mute=1";
iframe.width = "420";
iframe.height = "315"
iframe.style.marginBottom = 'auto';
iframe.style.marginTop = 'auto';
iframe.style.display = 'block';
document.body.appendChild(iframe);