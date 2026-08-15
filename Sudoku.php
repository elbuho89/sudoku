<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sudoku Búho Sabio - Edición Leyenda PHP</title>
    <style>
        :root {
            --primary: #5d4037;
            --primary-light: #8d6e63;
            --primary-dark: #3e2723;
            --accent: #ffb300;
            --accent-light: #fff8e1;
            --bg-body: #f7f5f2;
            /* Tamaño de celda adaptativo usando clamp para compatibilidad móvil total */
            --cell-size: clamp(28px, 9.5vw, 52px);
            --highlight-bg: #f5f0eb;
            --focus-bg: #d7ccc8;
            --match-bg: #ffe082;
        }

        * {
            box-sizing: border-box;
        }

        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: var(--bg-body); 
            text-align: center; 
            color: #333;
            margin: 0;
            padding: 10px;
            transition: background 0.3s;
        }

        header { margin-bottom: 5px; }
        h1 { margin: 5px 0; color: var(--primary); font-size: clamp(1.4rem, 5vw, 2.2rem); text-shadow: 1px 1px 2px rgba(0,0,0,0.1); }
        
        .owl { width: clamp(50px, 15vw, 80px); animation: bounce 2.5s infinite ease-in-out; filter: drop-shadow(0px 4px 6px rgba(0,0,0,0.1)); }
        @keyframes bounce { 0%, 100% {transform: translateY(0);} 50% {transform: translateY(-8px);} }
        
        /* Panel de control superior */
        .panel-control {
            background: white;
            padding: 12px 15px;
            border-radius: 12px;
            max-width: 720px;
            margin: 10px auto;
            box-shadow: 0 8px 16px rgba(93, 64, 55, 0.08);
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 12px;
        }

        .timers-container {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .stat-box { 
            font-size: clamp(13px, 3.5vw, 16px); 
            font-weight: bold; 
            color: var(--primary-dark); 
            background: #f0ebe6; 
            padding: 6px 12px; 
            border-radius: 20px; 
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .toggle-container { 
            display: flex; 
            align-items: center; 
            gap: 6px; 
            font-size: 13px; 
            font-weight: 600; 
            color: #d84315; 
            cursor: pointer; 
            background: #fbe9e7;
            padding: 6px 10px;
            border-radius: 15px;
        }
        
        /* Todos los botones agrupados en la parte superior */
        .btn-group {
            display: flex;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
            margin: 10px auto;
            max-width: 750px;
        }

        .grid-container {
            position: relative;
            width: fit-content;
            margin: 15px auto;
        }

        .grid { 
            display: grid; 
            grid-template-columns: repeat(9, var(--cell-size)); 
            justify-content: center; 
            border: 3px solid var(--primary-dark);
            border-radius: 4px;
            width: fit-content;
            box-shadow: 0 10px 25px rgba(93, 64, 55, 0.2);
            background: white;
            touch-action: manipulation;
        }

        /* Overlay de Pausa */
        #overlay-pausa {
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(62, 39, 35, 0.96);
            color: white;
            border-radius: 4px;
            z-index: 10;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            font-weight: bold;
            box-shadow: 0 12px 30px rgba(0,0,0,0.3);
        }

        input { 
            width: var(--cell-size); 
            height: var(--cell-size); 
            text-align: center; 
            font-size: calc(var(--cell-size) * 0.48); 
            border: 1px solid #e0e0e0; 
            box-sizing: border-box;
            outline: none;
            font-weight: 600;
            text-transform: uppercase;
            transition: all 0.15s ease;
            padding: 0;
            margin: 0;
            border-radius: 0;
            -webkit-appearance: none;
        }

        .b-right { border-right: 2px solid var(--primary-dark); }
        .b-bottom { border-bottom: 2px solid var(--primary-dark); }
        
        .fijo { background: #efebe9; color: var(--primary-dark); font-weight: bold; }
        .pista { background: #e8f5e9 !important; color: #2e7d32 !important; font-weight: bold; }
        .demo-auto { background: #e0f7fa !important; color: #006064 !important; font-weight: bold; }
        .error { background: #ffebee !important; color: #c62828 !important; font-weight: bold; animation: shake 0.2s ease-in-out 2; }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-2px); }
            75% { transform: translateX(2px); }
        }

        .relacionado { background: var(--highlight-bg); }
        .mismo-numero { background: var(--match-bg) !important; color: var(--primary-dark); font-weight: bold; }
        .celda-activa { background: var(--focus-bg) !important; box-shadow: inset 0 0 4px rgba(0,0,0,0.2); }

        .btn { 
            padding: 8px 12px; 
            background: var(--primary); 
            color: white; 
            border: none; 
            cursor: pointer; 
            border-radius: 8px; 
            font-weight: bold; 
            font-size: clamp(12px, 3vw, 14px);
            display: inline-flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 3px 5px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
        }
        .btn:hover { background: var(--primary-light); transform: translateY(-1px); }
        .btn:active { transform: translateY(1px); }
        .btn:disabled { background: #bcaaa4; cursor: not-allowed; transform: none; box-shadow: none; }
        
        .btn-pausa { background: #f57c00; }
        .btn-pausa:hover { background: #ef6c00; }
        .btn-pista { background: #2e7d32; }
        .btn-pista:hover { background: #388e3c; }
        .btn-solucionar { background: #0288d1; }
        .btn-solucionar:hover { background: #039be5; }
        .btn-borrar { background: #d84315; }
        .btn-borrar:hover { background: #e64a19; }
        .btn-imprimir { background: #78909c; }
        .btn-imprimir:hover { background: #90a4ae; }
        .btn-demo { background: #7b1fa2; }
        .btn-demo:hover { background: #8e24aa; }
        .btn-demo.activo { background: #e91e63; animation: pulse 1.5s infinite; }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.03); }
            100% { transform: scale(1); }
        }

        #mensaje { 
            font-size: clamp(14px, 3.5vw, 17px); 
            font-weight: bold; 
            margin: 10px auto; 
            max-width: 600px;
            min-height: 27px; 
            padding: 10px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        @media print {
            body { background: white; color: black; padding: 0; margin: 0; }
            header, .panel-control, .btn-group, #mensaje, #overlay-pausa { display: none !important; }
            .grid { border: 4px solid black !important; margin: 20px auto !important; box-shadow: none !important; display: grid !important; }
            input { border: 1px solid #000 !important; color: black !important; background: transparent !important; }
            .fijo { background: #e0e0e0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

    <header>
        <h1>Sudoku del Búho Sabio 🦉</h1>
        <img src="/1995535.png" class="owl" alt="Búho Sabio">
    </header>
    
    <!-- Panel de Control Superior -->
    <div class="panel-control">
        <div>
            <label style="font-weight: bold; font-size: 13px; color: var(--primary-dark);">Dificultad: </label>
            <select id="nivel" onchange="iniciarJuego()" style="padding: 5px 8px; font-size: 13px; border-radius: 6px; border: 2px solid var(--primary-light); outline:none; cursor:pointer;">
                <option value="muy-facil">1. Huevo Sabio (Muy Fácil)</option>
                <option value="facil" selected>2. Pichón Aprendiz (Fácil)</option>
                <option value="medio">3. Cazador Nocturno (Medio)</option>
                <option value="dificil">4. Guardián del Bosque (Difícil)</option>
                <option value="leyenda">5. Leyenda Ancestral 🔤 (Letras A-I)</option>
                <option value="extremo">6. Místico Extremo 🔢🔤 (Vocales + Números con 0)</option>
            </select>
        </div>
        
        <!-- Doble Contador -->
        <div class="timers-container">
            <div class="stat-box" title="Tiempo Normal Transcurrido">⏱️ Normal: <span id="tiempo-normal">00:00</span></div>
            <div class="stat-box" title="Tiempo Restante Contrarreloj" style="color: #c62828;">⏳ Contrarreloj: <span id="tiempo-contrarreloj">02:00</span></div>
        </div>
        
        <div class="toggle-container">
            <input type="checkbox" id="mode-contrarreloj" onchange="cambiarModoTiempo()" style="width:16px; height:16px; margin:0; cursor:pointer;">
            <label for="mode-contrarreloj" style="cursor:pointer; user-select:none;">Activar Contrarreloj</label>
        </div>
    </div>

    <!-- TODOS LOS BOTONES EN LA PARTE SUPERIOR -->
    <div class="btn-group">
        <button class="btn" onclick="iniciarJuego()">🔄 Nuevo Juego</button>
        <button class="btn btn-pausa" id="btn-pausa" onclick="togglePausa()">⏸️ Pausar</button>
        <button class="btn btn-pista" id="btn-pista-id" onclick="darPista()">💡 Pista (<span id="pistas-restantes">3</span>)</button>
        <button class="btn btn-solucionar" onclick="solucionarJuego(false)">✨ Solucionar</button>
        <button class="btn btn-demo" id="btn-demo" onclick="toggleModoDemo()">🎬 Modo Demo Infinito</button>
        <button class="btn btn-borrar" onclick="borrarProgreso()">🧹 Borrar Progreso</button>
        <button class="btn btn-imprimir" onclick="imprimirJuego()">🖨️ Imprimir</button>
    </div>

    <div class="grid-container">
        <div class="grid" id="sudoku-grid"></div>
        <div id="overlay-pausa">
            <span>⏸️ Tablero Oculto</span>
            <span style="font-size: 14px; font-weight: normal; margin-top: 8px;">El Búho observa en silencio...</span>
        </div>
    </div>

    <div id="mensaje"></div>

    <script>
        let solucionActual = [];
        let pistasDisponibles = 3;
        let intervaloTiempo = null;
        let segundosTranscurridos = 0;
        let tiempoRestanteContrarreloj = 120;
        let temporizadorCorriendo = false;
        let estaPausado = false;
        let modoSimbolosActual = 'numeros'; // 'numeros', 'letras', 'extremo'

        // Modo Demo
        let modoDemoActivo = false;
        let intervaloDemoPaso = null;
        let timeoutDemoCiclo = null;

        const juegoSimbolos = {
            numeros: ['1','2','3','4','5','6','7','8','9'],
            letras:  ['A','B','C','D','E','F','G','H','I'],
            extremo: ['0','1','2','3','A','E','I','O','U'] // Vocales + Números incluyendo el 0 (9 símbolos)
        };

        const matrizSemilla = [
            [5,3,4, 6,7,8, 9,1,2],
            [6,7,2, 1,9,5, 3,4,8],
            [1,9,8, 3,4,2, 5,6,7],
            [8,5,9, 7,6,1, 4,2,3],
            [4,2,6, 8,5,3, 7,9,1],
            [7,1,3, 9,2,4, 8,5,6],
            [9,6,1, 5,3,7, 2,8,4],
            [2,8,7, 4,1,9, 6,3,5],
            [3,4,5, 2,8,6, 1,7,9]
        ];

        function reproducirSonido(tipo) {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);

                if (tipo === 'click') {
                    osc.frequency.setValueAtTime(400, ctx.currentTime);
                    gain.gain.setValueAtTime(0.05, ctx.currentTime);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.05);
                } else if (tipo === 'derrota') {
                    osc.type = 'sawtooth';
                    osc.frequency.setValueAtTime(200, ctx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(80, ctx.currentTime + 0.6);
                    gain.gain.setValueAtTime(0.2, ctx.currentTime);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.6);
                } else if (tipo === 'victoria') {
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(300, ctx.currentTime);
                    osc.frequency.setValueAtTime(500, ctx.currentTime + 0.1);
                    osc.frequency.setValueAtTime(700, ctx.currentTime + 0.2);
                    gain.gain.setValueAtTime(0.2, ctx.currentTime);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.35);
                }
            } catch(e){}
        }

        function generarSudokuUnico(simbolos) {
            const numeros = [0,1,2,3,4,5,6,7,8];
            for (let i = numeros.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [numeros[i], numeros[j]] = [numeros[j], numeros[i]];
            }
            return matrizSemilla.map(fila => fila.map(val => simbolos[numeros[val - 1]]));
        }

        function formatoTiempo(totalSegs) {
            let min = Math.floor(totalSegs / 60).toString().padStart(2, '0');
            let seg = (totalSegs % 60).toString().padStart(2, '0');
            return `${min}:${seg}`;
        }

        function actualizarDisplayTiempo() {
            document.getElementById('tiempo-normal').innerText = formatoTiempo(segundosTranscurridos);
            document.getElementById('tiempo-contrarreloj').innerText = formatoTiempo(tiempoRestanteContrarreloj);
        }

        function cambiarModoTiempo() {
            detenerTemporizador();
            tiempoRestanteContrarreloj = 120;
            segundosTranscurridos = 0;
            actualizarDisplayTiempo();
            if (document.getElementById('mode-contrarreloj').checked) {
                arrancarTemporizador();
            }
        }

        function arrancarTemporizador() {
            if (temporizadorCorriendo || estaPausado) return;
            
            temporizadorCorriendo = true;

            intervaloTiempo = setInterval(() => {
                const esContrarreloj = document.getElementById('mode-contrarreloj').checked;
                
                // El contador normal siempre avanza
                segundosTranscurridos++;

                if (esContrarreloj) {
                    tiempoRestanteContrarreloj--;

                    if (tiempoRestanteContrarreloj <= 0) {
                        tiempoRestanteContrarreloj = 0;
                        actualizarDisplayTiempo();
                        detenerTemporizador();
                        reproducirSonido('derrota');
                        solucionarJuego(true); // Se auto-resuelve por tiempo agotado
                        return;
                    }
                }
                actualizarDisplayTiempo();
            }, 1000);
        }

        function detenerTemporizador() {
            clearInterval(intervaloTiempo);
            temporizadorCorriendo = false;
        }

        function reiniciarTemporizador() {
            detenerTemporizador();
            segundosTranscurridos = 0;
            tiempoRestanteContrarreloj = 120;
            actualizarDisplayTiempo();
        }

        function togglePausa() {
            if (modoDemoActivo) return;

            const overlay = document.getElementById('overlay-pausa');
            const grid = document.getElementById('sudoku-grid');
            const btnPausa = document.getElementById('btn-pausa');

            if (!estaPausado) {
                estaPausado = true;
                detenerTemporizador();
                grid.style.visibility = 'hidden';
                overlay.style.display = 'flex';
                btnPausa.innerHTML = '▶️ Reanudar';
            } else {
                estaPausado = false;
                grid.style.visibility = 'visible';
                overlay.style.display = 'none';
                btnPausa.innerHTML = '⏸️ Pausar';
                
                if (segundosTranscurridos > 0 || document.getElementById('mode-contrarreloj').checked) {
                    arrancarTemporizador();
                }
            }
        }

        function iniciarJuego(esLoopDemo = false) {
            detenerModoDemoProcesos();
            if (!esLoopDemo) {
                modoDemoActivo = false;
                document.getElementById('btn-demo').classList.remove('activo');
                document.getElementById('btn-demo').innerText = '🎬 Modo Demo Infinito';
            }

            const nivel = document.getElementById('nivel').value;
            const grid = document.getElementById('sudoku-grid');
            document.getElementById('mensaje').innerText = '';
            grid.innerHTML = '';

            if (estaPausado) togglePausa();
            
            pistasDisponibles = 3;
            document.getElementById('btn-pista-id').disabled = false;
            document.getElementById('pistas-restantes').innerText = pistasDisponibles;

            reiniciarTemporizador();

            // Configurar tipo de símbolos
            if (nivel === 'leyenda') {
                modoSimbolosActual = 'letras';
            } else if (nivel === 'extremo') {
                modoSimbolosActual = 'extremo';
            } else {
                modoSimbolosActual = 'numeros';
            }

            const simbolos = juegoSimbolos[modoSimbolosActual];
            const solucion = generarSudokuUnico(simbolos);
            solucionActual = solucion.map(f => [...f]);

            // Cantidad de casillas vacías según nivel
            let vacias = 25; 
            if (nivel === 'muy-facil') vacias = 15;
            if (nivel === 'facil') vacias = 25;
            if (nivel === 'medio') vacias = 35;
            if (nivel === 'dificil') vacias = 48;
            if (nivel === 'leyenda') vacias = 55;
            if (nivel === 'extremo') vacias = 62; // Nivel Extremo

            let tableroJuego = solucion.map(f => [...f]);
            let contadorOcultas = 0;
            while (contadorOcultas < vacias) {
                let r = Math.floor(Math.random() * 9);
                let c = Math.floor(Math.random() * 9);
                if (tableroJuego[r][c] !== '') {
                    tableroJuego[r][c] = '';
                    contadorOcultas++;
                }
            }

            for (let i = 0; i < 9; i++) {
                for (let j = 0; j < 9; j++) {
                    let input = document.createElement('input');
                    input.type = 'text';
                    input.maxLength = 1;
                    
                    if (j === 2 || j === 5) input.classList.add('b-right');
                    if (i === 2 || i === 5) input.classList.add('b-bottom');

                    input.dataset.row = i;
                    input.dataset.col = j;
                    input.dataset.box = Math.floor(i / 3) * 3 + Math.floor(j / 3);

                    if (tableroJuego[i][j] !== '') {
                        input.value = tableroJuego[i][j];
                        input.disabled = true;
                        input.classList.add('fijo');
                        input.onfocus = function() { iluminarEntorno(this); };
                        input.onblur = function() { limpiarEntorno(); };
                    } else {
                        input.oninput = function() { evaluarCelda(this); iluminarEntorno(this); };
                        input.onfocus = function() { iluminarEntorno(this); };
                        input.onblur = function() { limpiarEntorno(); };
                    }

                    grid.appendChild(input);
                }
            }

            if (modoDemoActivo) {
                ejecutarCicloDemo();
            }
        }

        function iluminarEntorno(celdaActiva) {
            limpiarEntorno();
            const fila = celdaActiva.dataset.row;
            const col = celdaActiva.dataset.col;
            const box = celdaActiva.dataset.box;
            const valorActivo = celdaActiva.value.toUpperCase();

            const inputs = document.querySelectorAll('#sudoku-grid input');
            inputs.forEach(input => {
                if (input.dataset.row === fila || input.dataset.col === col || input.dataset.box === box) {
                    input.classList.add('relacionado');
                }
                if (valorActivo && input.value.toUpperCase() === valorActivo) {
                    input.classList.add('mismo-numero');
                }
            });
            celdaActiva.classList.add('celda-activa');
        }

        function limpiarEntorno() {
            const inputs = document.querySelectorAll('#sudoku-grid input');
            inputs.forEach(input => {
                input.classList.remove('relacionado', 'celda-activa', 'mismo-numero');
            });
        }

        function evaluarCelda(input) {
            if (modoDemoActivo) return;
            reproducirSonido('click');

            // Filtrar input según el modo de símbolos actual
            if (modoSimbolosActual === 'letras') {
                input.value = input.value.replace(/[^a-iA-I]/g, '').toUpperCase();
            } else if (modoSimbolosActual === 'extremo') {
                // Filtro para Vocales + Números 0, 1, 2, 3
                input.value = input.value.replace(/[^0-3aAeEiIoOuU]/g, '').toUpperCase();
            } else {
                input.value = input.value.replace(/[^1-9]/g, '');
            }
            
            // Iniciar temporizador automáticamente al escribir si no estaba activo
            if (input.value !== '') {
                arrancarTemporizador();
            }

            input.classList.remove('error');
            comprobarSiLleno();
        }

        function comprobarSiLleno() {
            const inputs = Array.from(document.querySelectorAll('#sudoku-grid input'));
            const todoLleno = inputs.every(inp => inp.value !== '');

            if (todoLleno) {
                detenerTemporizador();
                
                let correcto = inputs.every(input => {
                    const r = parseInt(input.dataset.row);
                    const c = parseInt(input.dataset.col);
                    return input.value.toUpperCase() === solucionActual[r][c];
                });

                const mensaje = document.getElementById('mensaje');
                const esContrarreloj = document.getElementById('mode-contrarreloj').checked;

                if (correcto) {
                    reproducirSonido('victoria');
                    let tiempoTranscurridosTexto = formatoTiempo(segundosTranscurridos);
                    
                    if (esContrarreloj) {
                        let tiempoDemoradoContrarreloj = 120 - tiempoRestanteContrarreloj;
                        mensaje.innerHTML = `🏆 ¡VICTORIA EN CONTRARRELOJ! 🦉<br><em>Te demoraste <strong>${formatoTiempo(tiempoDemoradoContrarreloj)}</strong> en superar el desafío (Tiempo total normal: ${tiempoTranscurridosTexto}).</em>`;
                    } else {
                        mensaje.innerHTML = `🦉 <em>'¡Impresionante! Has dominado el tablero en <strong>${tiempoTranscurridosTexto}</strong>.'</em>`;
                    }
                    mensaje.style.color = "#2e7d32";
                } else {
                    reproducirSonido('derrota');
                    mensaje.innerHTML = "🦉 <em>'El tablero está lleno pero hay incoherencias. Revisa tus movimientos.'</em>";
                    mensaje.style.color = "#c62828";
                }
            }
        }

        function darPista() {
            if (pistasDisponibles <= 0 || modoDemoActivo) return;

            arrancarTemporizador();

            const inputs = Array.from(document.querySelectorAll('#sudoku-grid input:not([disabled])'));
            const celdasCandidatas = inputs.filter(input => {
                const r = parseInt(input.dataset.row);
                const c = parseInt(input.dataset.col);
                return !input.value || input.value.toUpperCase() !== solucionActual[r][c];
            });

            if (celdasCandidatas.length === 0) return;

            const celdaElegida = celdasCandidatas[Math.floor(Math.random() * celdasCandidatas.length)];
            const row = parseInt(celdaElegida.dataset.row);
            const col = parseInt(celdaElegida.dataset.col);

            celdaElegida.value = solucionActual[row][col];
            celdaElegida.disabled = true;
            celdaElegida.classList.remove('error');
            celdaElegida.classList.add('pista');

            pistasDisponibles--;
            document.getElementById('pistas-restantes').innerText = pistasDisponibles;

            if (pistasDisponibles === 0) {
                document.getElementById('btn-pista-id').disabled = true;
            }

            comprobarSiLleno();
        }

        function solucionarJuego(porTiempoAgotado = false) {
            const inputs = document.querySelectorAll('#sudoku-grid input');
            if (inputs.length === 0) return;

            inputs.forEach(input => {
                const r = parseInt(input.dataset.row);
                const c = parseInt(input.dataset.col);
                
                if (!input.classList.contains('fijo')) {
                    input.value = solucionActual[r][c];
                    input.classList.remove('error');
                    if (!modoDemoActivo) {
                        input.classList.add('pista');
                    }
                }
            });

            detenerTemporizador();
            const mensaje = document.getElementById('mensaje');

            if (porTiempoAgotado) {
                mensaje.innerHTML = "⏰ 🦉 <em>'¡El tiempo Contrarreloj ha expirado! El Sudoku se ha resuelto solo. ¡Inténtalo de nuevo!'</em>";
                mensaje.style.color = "#d84315";
            } else if (!modoDemoActivo) {
                mensaje.innerHTML = "🦉 <em>'He revelado todos los secretos del tablero.'</em>";
                mensaje.style.color = "#0288d1";
            }
        }

        // MODO DEMO INFINITO
        function toggleModoDemo() {
            const btnDemo = document.getElementById('btn-demo');
            if (!modoDemoActivo) {
                modoDemoActivo = true;
                btnDemo.classList.add('activo');
                btnDemo.innerText = '⏹️ Detener Demo';
                iniciarJuego(true);
            } else {
                modoDemoActivo = false;
                btnDemo.classList.remove('activo');
                btnDemo.innerText = '🎬 Modo Demo Infinito';
                detenerModoDemoProcesos();
                iniciarJuego(false);
            }
        }

        function detenerModoDemoProcesos() {
            if (intervaloDemoPaso) clearInterval(intervaloDemoPaso);
            if (timeoutDemoCiclo) clearTimeout(timeoutDemoCiclo);
        }

        function ejecutarCicloDemo() {
            detenerTemporizador();
            detenerModoDemoProcesos();

            const nivel = document.getElementById('nivel').value;
            // Dificultad alta (dificil, leyenda, extremo) = 60 seg (1 min). Simples = 40 seg.
            const esDificil = ['dificil', 'leyenda', 'extremo'].includes(nivel);
            const duracionTotalSegundos = esDificil ? 60 : 40;

            const inputsVacios = Array.from(document.querySelectorAll('#sudoku-grid input:not(.fijo)'));
            inputsVacios.forEach(input => {
                input.value = '';
                input.disabled = true;
            });

            const mensaje = document.getElementById('mensaje');
            mensaje.innerHTML = `🎬 <strong>Modo Demo: Demostración automática en progreso...`;
            mensaje.style.color = "#7b1fa2";

            const totalPasos = inputsVacios.length;
            const intervaloMs = (duracionTotalSegundos * 1000) / totalPasos;
            let pasoActual = 0;

            intervaloDemoPaso = setInterval(() => {
                if (!modoDemoActivo) return;

                if (pasoActual < totalPasos) {
                    const input = inputsVacios[pasoActual];
                    const r = parseInt(input.dataset.row);
                    const c = parseInt(input.dataset.col);
                    input.value = solucionActual[r][c];
                    input.classList.add('demo-auto');
                    reproducirSonido('click');
                    pasoActual++;
                } else {
                    clearInterval(intervaloDemoPaso);
                    mensaje.innerHTML = `✨ <strong>¡Demo Completada!</strong> Generando un nuevo Sudoku automáticamente...`;
                    mensaje.style.color = "#2e7d32";
                    reproducirSonido('victoria');

                    // Cambiar a otro puzzle infinitamente tras 3 segundos
                    timeoutDemoCiclo = setTimeout(() => {
                        if (modoDemoActivo) {
                            // Alternar o reiniciar nuevo tablero en el ciclo infinito
                            iniciarJuego(true);
                        }
                    }, 3000);
                }
            }, intervaloMs);
        }

        function borrarProgreso() {
            if (modoDemoActivo) return;
            const inputs = document.querySelectorAll('#sudoku-grid input:not(.fijo)');
            inputs.forEach(input => {
                input.value = '';
                input.disabled = false;
                input.classList.remove('error', 'pista', 'relacionado', 'celda-activa', 'mismo-numero', 'demo-auto');
            });
            document.getElementById('mensaje').innerText = '';
        }

        function imprimirJuego() {
            window.print();
        }

        window.onload = function() {
            iniciarJuego();
        };
    </script>
</body>
</html>