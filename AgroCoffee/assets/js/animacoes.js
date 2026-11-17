/* ==========================================================================
 *  animacoes.js
 *  - Revela cards, títulos e caixas quando entram na tela (rolagem).
 *  - Deixa o cabeçalho com sombra e logo menor depois que a página rola.
 *  O CSS só esconde os elementos se este script rodar (classe .js no <html>).
 * ========================================================================== */

document.documentElement.classList.add("js");

document.addEventListener("DOMContentLoaded", function () {
    // Mesma lista usada no style.css
    var seletor = ".card, .cardProduto, .atalho, .caixaResumo, section:not(.hero) > h2, .headline, #contato > p, #contato > a";
    var elementos = document.querySelectorAll(seletor);

    // Atraso em cascata para itens que estão lado a lado (cards da mesma grade)
    elementos.forEach(function (el) {
        var irmaos = Array.prototype.filter.call(el.parentElement.children, function (filho) {
            return filho.matches(seletor);
        });
        var posicao = irmaos.indexOf(el);
        el.style.setProperty("--atraso", (posicao % 4) * 0.12 + "s");
    });

    if ("IntersectionObserver" in window) {
        var observador = new IntersectionObserver(function (entradas) {
            entradas.forEach(function (entrada) {
                if (entrada.isIntersecting) {
                    entrada.target.classList.add("visivel");
                    observador.unobserve(entrada.target);
                }
            });
        }, { threshold: 0.12, rootMargin: "0px 0px -40px 0px" });

        elementos.forEach(function (el) {
            observador.observe(el);
        });
    } else {
        elementos.forEach(function (el) {
            el.classList.add("visivel");
        });
    }

    // Cabeçalho muda depois que a página rola um pouco
    var cabecalho = document.querySelector("header");
    if (cabecalho) {
        var verificar = function () {
            cabecalho.classList.toggle("rolou", window.scrollY > 20);
        };
        window.addEventListener("scroll", verificar, { passive: true });
        verificar();
    }
});
