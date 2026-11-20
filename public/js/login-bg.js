/**
 * login-bg.js
 * Animated WebGL shader background for the login page.
 * Indigo/Purple flowing lines — dark base.
 */
(function () {
    /* ---- Canvas setup ---- */
    const canvas = document.getElementById('login-bg-canvas');
    const gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');

    function syncSize() {
        if (canvas.width !== window.innerWidth || canvas.height !== window.innerHeight) {
            canvas.width  = window.innerWidth;
            canvas.height = window.innerHeight;
        }
    }
    window.addEventListener('resize', syncSize);
    syncSize();

    if (!gl) return; // fallback — CSS dark bg already applied

    /* ---- Shaders ---- */
    const VS = `
        attribute vec2 a_position;
        varying vec2 v_texCoord;
        void main() {
            v_texCoord  = a_position * 0.5 + 0.5;
            gl_Position = vec4(a_position, 0.0, 1.0);
        }
    `;

    const FS = `
        precision highp float;
        varying vec2 v_texCoord;
        uniform float u_time;
        uniform vec2  u_resolution;

        float hash(vec2 p) {
            p = fract(p * vec2(123.34, 456.21));
            p += dot(p, p + 45.32);
            return fract(p.x * p.y);
        }

        float noise(vec2 p) {
            vec2 i = floor(p), f = fract(p);
            float a = hash(i), b = hash(i + vec2(1,0)),
                  c = hash(i + vec2(0,1)), d = hash(i + vec2(1,1));
            vec2 u = f * f * (3.0 - 2.0 * f);
            return mix(a,b,u.x) + (c-a)*u.y*(1.0-u.x) + (d-b)*u.x*u.y;
        }

        void main() {
            vec2 uv = (gl_FragCoord.xy * 2.0 - u_resolution.xy) / min(u_resolution.y, u_resolution.x);
            float t  = u_time * 0.2;
            vec3 col = vec3(0.05, 0.05, 0.2);

            for (float i = 1.0; i < 4.0; i++) {
                uv.x += 0.3 / i * sin(i * 3.0 * uv.y + t + noise(uv * 0.5) * 2.0);
                uv.y += 0.3 / i * cos(i * 3.0 * uv.x + t + noise(uv * 0.5) * 2.0);
                col  += abs(1.0 / (200.0 * uv.y)) * vec3(0.35, 0.3, 0.9);
            }

            if (noise(v_texCoord * 500.0 + u_time) > 0.98) col += 0.2;
            gl_FragColor = vec4(col, 1.0);
        }
    `;

    /* ---- Compile helper ---- */
    function compileShader(type, src) {
        const s = gl.createShader(type);
        gl.shaderSource(s, src);
        gl.compileShader(s);
        return s;
    }

    /* ---- Program ---- */
    const prog = gl.createProgram();
    gl.attachShader(prog, compileShader(gl.VERTEX_SHADER,   VS));
    gl.attachShader(prog, compileShader(gl.FRAGMENT_SHADER, FS));
    gl.linkProgram(prog);
    gl.useProgram(prog);

    /* ---- Geometry: full-screen quad ---- */
    const buf = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, buf);
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1,-1, 1,-1, -1,1, 1,1]), gl.STATIC_DRAW);
    const pos = gl.getAttribLocation(prog, 'a_position');
    gl.enableVertexAttribArray(pos);
    gl.vertexAttribPointer(pos, 2, gl.FLOAT, false, 0, 0);

    /* ---- Uniforms ---- */
    const uTime = gl.getUniformLocation(prog, 'u_time');
    const uRes  = gl.getUniformLocation(prog, 'u_resolution');

    /* ---- Render loop ---- */
    function render(ms) {
        syncSize();
        gl.viewport(0, 0, canvas.width, canvas.height);
        gl.uniform1f(uTime, ms * 0.001);
        gl.uniform2f(uRes, canvas.width, canvas.height);
        gl.drawArrays(gl.TRIANGLE_STRIP, 0, 4);
        requestAnimationFrame(render);
    }
    requestAnimationFrame(render);
})();
