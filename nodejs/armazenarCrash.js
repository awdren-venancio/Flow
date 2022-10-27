const puppeteer = require('puppeteer');

(async () => {
    let resultadoAnterior = [];
    let i = 0;
    let salvar = false;
    let data = '';
    let hora = '';

    const browser = await puppeteer.launch({headless: false}); 
    const page = await browser.newPage();  
    await page.setDefaultNavigationTimeout(0);  

    await page.goto('https://blaze.com/pt/games/crash');
    
    await page.setViewport({width: 1800,height: 1000});
    await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36');

    var Datastore = require('nedb');
    var crash = new Datastore({ filename: 'crash.db', autoload: true });

    while(1){
        try {
                
            data = await page.evaluate(()=> {
                let resultados = document.querySelector('[class="entries"]').innerText;
                
                return {
                    resultados
                }
            })
            
            let test = Object.values(data);

            let resultado = test[0];
            resultado = resultado.split('X');

            salvar = false;

            if (resultadoAnterior != []) {
                i = 0;
                
                while (i < 5 && salvar == false) {
                    if (resultado[i] != resultadoAnterior[i]) salvar = true;
                    i++;
                }
                
            } else {
                salvar = true;
            }
            
            if (salvar) {
                resultadoAnterior = resultado;
                
                now = new Date
                data = now.getDate() + '/' + (Number(now.getMonth())+1).toString() + '/' + now.getFullYear();
                hora = new Date().toLocaleTimeString().substring(0,5);

                console.log('Salvou: ' + resultado[0] + ' - ' +  data + ' - ' + hora);
                
                var res = {
                    valor: Number(resultado[0]),
                    data: data,
                    hora: hora
                };
                crash.insert(res);
            }
            
            await page.waitForTimeout(3000);
        }
        catch(e){
            console.log('ERRO: ' + e);
            await page.waitForTimeout(3000);
        }

    }
})();