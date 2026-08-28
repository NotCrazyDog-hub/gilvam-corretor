<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Novo Contrato de Corretagem</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 2rem 1rem;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        h1 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }
        fieldset {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1rem 1.25rem 1.25rem;
            margin-bottom: 1.5rem;
            background: #fff;
        }
        legend {
            font-weight: 600;
            padding: 0 0.5rem;
        }
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }
        .col-span-2 {
            grid-column: span 2;
        }
        .input {
            width: 100%;
            box-sizing: border-box;
            padding: 0.5rem 0.65rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 0.9rem;
        }
        .input:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 1px;
        }
        label.field-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 500;
            margin: 1rem 0 0.35rem;
        }
        .editor-box {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 0.6rem;
            min-height: 100px;
            background: #fff;
            font-size: 0.9rem;
        }
        .editor-box:focus-within {
            outline: 2px solid #3b82f6;
            outline-offset: 1px;
        }
        .editor-box p {
            margin: 0 0 0.5rem;
        }
        button[type="submit"] {
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 0.65rem 1.5rem;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
        }
        button[type="submit"]:hover {
            background: #1d4ed8;
        }
        button[type="submit"]:disabled {
            background: #93c5fd;
            cursor: not-allowed;
        }
        #resultado {
            margin-top: 1.5rem;
            padding: 1rem;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 8px;
        }
        #resultado a {
            color: #047857;
            font-weight: 600;
            text-decoration: underline;
        }
        #erro {
            margin-top: 1.5rem;
            padding: 1rem;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            color: #b91c1c;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Novo Contrato de Corretagem</h1>

        <form id="contract-form" method="POST" action="{{ route('contracts.store') }}">
            @csrf

            <fieldset>
                <legend>I. Contratante</legend>
                <div class="grid">
                    <input name="contratante[nome]" placeholder="Nome" class="input" required>
                    <input name="contratante[estado_civil]" placeholder="Estado civil" class="input">
                    <input name="contratante[cpf]" placeholder="CPF" class="input" required>
                    <input name="contratante[conjuge_nome]" placeholder="Nome do cônjuge" class="input">
                    <input name="contratante[conjuge_cpf]" placeholder="CPF do cônjuge" class="input">
                    <input name="contratante[endereco]" placeholder="Endereço" class="input col-span-2">
                    <input name="contratante[telefones]" placeholder="Telefones" class="input">
                    <input name="contratante[profissao]" placeholder="Profissão" class="input">
                    <input name="contratante[identidade]" placeholder="Identidade" class="input">
                    <input name="contratante[conjuge_identidade]" placeholder="Identidade do cônjuge" class="input">
                </div>
            </fieldset>

            <fieldset>
                <legend>II. Contratado(a)</legend>
                <div class="grid">
                    <input name="contratado[nome]" placeholder="Nome / Razão social" class="input" required>
                    <input name="contratado[cpf_cnpj]" placeholder="CPF ou CNPJ" class="input" required>
                    <input name="contratado[endereco]" placeholder="Endereço" class="input col-span-2">
                    <input name="contratado[telefones]" placeholder="Telefones" class="input">
                    <input name="contratado[creci]" placeholder="CRECI" class="input">
                </div>
            </fieldset>

            <fieldset>
                <legend>III. Objeto do Contrato</legend>
                <div class="grid">
                    <input name="imovel[tipo]" placeholder="Tipo de imóvel" class="input">
                    <input name="imovel[localizacao]" placeholder="Localização" class="input col-span-2">
                    <input name="imovel[documento_propriedade]" placeholder="Documento de propriedade" class="input">
                    <input name="imovel[matricula]" placeholder="Matrícula" class="input">
                    <input name="imovel[inscricao_prefeitura]" placeholder="Inscrição prefeitura" class="input">
                    <input name="imovel[preco_venda]" placeholder="Preço para venda" class="input">
                </div>

                <label class="field-label" for="editor-outros-dados">Outros dados</label>
                <div id="editor-outros-dados" class="editor-box"></div>
                <input type="hidden" name="imovel[outros_dados]" id="input-outros-dados">

                <label class="field-label" for="editor-condicoes">Condições de pagamento</label>
                <div id="editor-condicoes" class="editor-box"></div>
                <input type="hidden" name="imovel[condicoes_pagamento]" id="input-condicoes">
            </fieldset>

            <button type="submit" id="submit-btn">Gerar contrato</button>
        </form>

        <div id="resultado" class="hidden">
            <a id="link-download" href="#" target="_blank">Baixar contrato gerado</a>
        </div>

        <div id="erro" class="hidden"></div>
    </div>

    <script type="module">
        import { Editor } from '@tiptap/core';
        import StarterKit from '@tiptap/starter-kit';

        function mountEditor(editorSelector, hiddenInputSelector) {
            const el = document.querySelector(editorSelector);
            const hiddenInput = document.querySelector(hiddenInputSelector);

            if (!el || !hiddenInput) {
                console.error(`Elemento não encontrado: ${editorSelector} ou ${hiddenInputSelector}`);
                return null;
            }

            return new Editor({
                element: el,
                extensions: [StarterKit],
                content: '',
                onUpdate: ({ editor }) => {
                    // texto puro por enquanto — o backend faz substituição direta no XML do docx
                    hiddenInput.value = editor.getText();
                },
            });
        }

        function formDataToNestedObject(formData) {
            const result = {};
            for (const [key, value] of formData.entries()) {
                if (key === '_token') continue;
                const match = key.match(/^([^\[]+)\[([^\]]+)\]$/);
                if (match) {
                    const [, group, field] = match;
                    result[group] = result[group] || {};
                    result[group][field] = value;
                } else {
                    result[key] = value;
                }
            }
            return result;
        }

        document.addEventListener('DOMContentLoaded', () => {
            try {
                mountEditor('#editor-outros-dados', '#input-outros-dados');
                mountEditor('#editor-condicoes', '#input-condicoes');
            } catch (err) {
                console.error('Falha ao montar o TipTap:', err);
            }

            const form = document.getElementById('contract-form');
            const submitBtn = document.getElementById('submit-btn');
            const resultado = document.getElementById('resultado');
            const linkDownload = document.getElementById('link-download');
            const erroBox = document.getElementById('erro');

            if (!form) {
                console.error('Form #contract-form não encontrado no DOM.');
                return;
            }

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                erroBox.classList.add('hidden');
                resultado.classList.add('hidden');
                submitBtn.disabled = true;
                submitBtn.textContent = 'Gerando...';

                try {
                    const formData = new FormData(form);
                    const payload = formDataToNestedObject(formData);

                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(payload),
                    });

                    if (!response.ok) {
                        const body = await response.json().catch(() => null);
                        const msg = body?.message || 'Confira os campos obrigatórios.';
                        throw new Error(msg);
                    }

                    const data = await response.json();
                    linkDownload.href = data.download_url;
                    resultado.classList.remove('hidden');
                } catch (err) {
                    erroBox.textContent = 'Erro ao gerar contrato: ' + err.message;
                    erroBox.classList.remove('hidden');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Gerar contrato';
                }
            });
        });
    </script>
</body>
</html>