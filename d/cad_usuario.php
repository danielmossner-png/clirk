<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="css/style2.css" />
        <title>CRUD - Cadastrar Usuário Docente</title>
        <style>
            .requisitos-senha {
                background-color: #000000;
                color: #ffffff;
                border: 1px solid #333333;
                border-radius: 5px;
                padding: 12px;
                margin-top: 5px;
                margin-bottom: 15px;
                width: 320px;
                font-size: 13px;
            }
            .requisitos-senha ul {
                margin: 8px 0 0 0;
                padding-left: 20px;
            }
            .requisitos-senha li {
                color: #ffffff;
                margin-bottom: 3px;
            }
            .checkbox-group {
                margin: 10px 0;
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                max-width: 400px;
            }
            .checkbox-group label {
                font-weight: normal;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <h1>PÁGINA DE CADASTRO - DOCENTE</h1>
        <h2><a href="index.php">Voltar para o Login</a><br></h2>
        <h1>Cadastrar Professor</h1>

        <?php
        if(isset($_SESSION['msg'])){
            echo $_SESSION['msg'];
            unset($_SESSION['msg']);
        }
        ?>

        <form method="POST" action="proc_cad_usuario.php" id="formCadastro">
            <!-- Dados Pessoais -->
            <label>Nome: </label>
            <input type="text" id="nome" name="nome" placeholder="Digite seu nome" required><br><br> 

            <label>Sobrenome: </label>
            <input type="text" id="sobrenome" name="sobrenome" placeholder="Digite seu sobrenome" required><br><br>

            <label>CPF / Matrícula Funcional: </label>
            <input type="text" id="matricula" name="matricula" placeholder="Digite seu CPF ou Matrícula" required><br><br>

            <label>E-mail Institucional (@professor): </label>
            <input type="email" id="email" name="email" placeholder="nome.sobrenome@professor.pr.gov.br" readonly required><br><br>
                    
            <!-- Autenticação -->
            <label>Senha: </label>
            <input type="password" name="pass" id="pass" placeholder="Digite sua senha" required><br>

            <div class="requisitos-senha">
                <strong>A senha deve conter obrigatoriamente:</strong>
                <ul>
                    <li>📏 No mínimo 8 caracteres</li>
                    <li>🔤 Letras minúsculas (a-z)</li>
                    <li>🔠 Letras maiúsculas (A-Z)</li>
                    <li>🔢 Números (0-9)</li>
                    <li>🔣 Caracteres especiais (!, @, #, $, %, etc.)</li>
                </ul>
            </div>

            <!-- Contato e Endereço -->
            <label>Endereço: </label>
            <input type="text" name="endereco" placeholder="Rua, número e bairro" required><br><br>

            <label>Telefone / WhatsApp: </label>
            <input type="tel" name="telefone" placeholder="(45) 99999-9999" required><br><br>

            <!-- Perfil Profissional -->
            <label>Titulação / Formação Acadêmica: </label>
            <select name="titulacao" id="titulacao" required>
                <option value="">Selecione a formação</option>
                <option value="Graduação / Licenciatura">Graduação / Licenciatura</option>
                <option value="Pós-Graduação / Especialização">Pós-Graduação / Especialização</option>
                <option value="Mestrado">Mestrado</option>
                <option value="Doutorado">Doutorado</option>
            </select><br><br>

            <!-- Localização e Atuação -->
            <label>Estado de Atuação: </label>
            <select id="estado" name="estado" required>
                <option value="">Selecione o Estado</option>
                <option value="PR">Paraná (PR)</option>
            </select><br><br>

            <label>Cidade: </label>
            <input type="text" id="cidade" name="cidade" list="lista_cidades" placeholder="Selecione primeiro o Estado" disabled required>
            <datalist id="lista_cidades"></datalist><br><br>

            <label>Escola / Colégio de Lotação: </label>
            <select id="escola" name="escola" disabled required>
                <option value="">Selecione primeiro a Cidade</option>
            </select><br><br>

            <label>Componente Curricular / Disciplina Principal: </label>
            <select id="disciplina" name="disciplina" disabled required>
                <option value="">Selecione primeiro a Escola</option>
            </select><br><br>
            
            <input type="submit" value="Cadastrar Professor">
        </form>

        <script>
// Validação da senha no envio do formulário
document.getElementById('formCadastro').addEventListener('submit', function(e) {
    const senha = document.getElementById('pass').value;
    const regexSenha = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/;

    if (!regexSenha.test(senha)) {
        e.preventDefault();
        alert('A senha não cumpre os requisitos:\n- Mínimo 8 caracteres\n- Letra maiúscula e minúscula\n- Número\n- Caractere especial');
    }
});

function removerAcentos(texto) {
    return texto.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase().trim();
}

// Geração do e-mail com domínio de professor
function gerarEmailAutomatico() {
    const nomeInput = document.getElementById('nome').value;
    const sobrenomeInput = document.getElementById('sobrenome').value;

    if (nomeInput && sobrenomeInput) {
        // Pega a primeira palavra do nome e a primeira do sobrenome
        const primeiroNome = removerAcentos(nomeInput).split(' ')[0];
        const primeiroSobrenome = removerAcentos(sobrenomeInput).split(' ')[0];

        if (primeiroNome !== '' && primeiroSobrenome !== '') {
            document.getElementById('email').value = `${primeiroNome}.${primeiroSobrenome}@professor.pr.gov.br`;
        } else {
            document.getElementById('email').value = '';
        }
    } else {
        document.getElementById('email').value = '';
    }
}

// Escuta tanto a digitação ('input') quanto a mudança/autofill ('change')
const elNome = document.getElementById('nome');
const elSobrenome = document.getElementById('sobrenome');

elNome.addEventListener('input', gerarEmailAutomatico);
elNome.addEventListener('change', gerarEmailAutomatico);

elSobrenome.addEventListener('input', gerarEmailAutomatico);
elSobrenome.addEventListener('change', gerarEmailAutomatico);

// Listas e encadeamentos de locais
const cidadesPR = [
    "Abatiá","Adrianópolis","Agudos do Sul","Almirante Tamandaré","Altamira do Paraná","Alto Paraíso","Alto Paraná","Alto Piquiri","Altônia","Alvorada do Sul","Amaporã","Ampére","Anahy","Andirá","Ângulo","Antonina","Antônio Olinto","Apucarana","Arapongas","Arapoti","Arapuã","Araruna","Araucária","Ariranha do Ivaí","Assaí","Assis Chateaubriand","Astorga","Atalaia","Balsa Nova","Bandeirantes","Barbosa Ferraz","Barra do Jacaré","Barracão","Bela Vista da Caroba","Bela Vista do Paraíso","Bituruna","Boa Esperança","Boa Esperança do Iguaçu","Boa Ventura de São Roque","Boa Vista da Aparecida","Bocaiúva do Sul","Bom Jesus do Sul","Bom Sucesso","Bom Sucesso do Sul","Borrazópolis","Braganey","Brasilândia do Sul","Cafeara","Cafelândia","Cafezal do Sul","Califórnia","Cambará","Cambé","Cambira","Campina da Lagoa","Campina do Simão","Campina Grande do Sul","Campo Bonito","Campo do Tenente","Campo Largo","Campo Mourão","Cândido de Abreu","Candói","Cantagalo","Capanema","Capitão Leônidas Marques","Carambeí","Carlópolis","Cascavel","Castro","Catanduvas","Centenário do Sul","Cerro Azul","Céu Azul","Chopinzinho","Cianorte","Cidade Gaúcha","Clevelândia","Colombo","Colorado","Congonhinhas","Conselheiro Mairinck","Contenda","Corbélia","Cornélio Procópio","Coronel Domingos Soares","Coronel Vivida","Corumbataí do Sul","Cruz Machado","Cruzeiro do Oeste","Cruzeiro do Sul","Cruzmaltina","Curitiba","Curiúva","Diamante d'Oeste","Diamante do Norte","Diamante do Sul","Dois Vizinhos","Douradina","Doutor Camargo","Doutor Ulysses","Enéas Marques","Engenheiro Beltrão","Entre Rios do Oeste","Esperança Nova","Espigão Alto do Iguaçu","Farol","Faxinal","Fazenda Rio Grande","Fênix","Fernandes Pinheiro","Figueira","Flor da Serra do Sul","Floraí","Floresta","Florestópolis","Flórida","Formosa do Oeste","Foz do Iguaçu","Foz do Jordão","Francisco Alves","Francisco Beltrão","General Carneiro","Godoy Moreira","Grandes Rios","Guaíra","Guairaçá","Guamiranga","Guapirama","Guaporema","Guaraci","Guaraniaçu","Guarapuava","Guaratuba","Guaraqueçaba","Honório Serpa","Ibaiti","Ibema","Ibiporã","Icaraíma","Iguaraçu","Iguatu","Imbaú","Imbituva","Inácio Martins","Inajá","Indianópolis","Ipiranga","Iporã","Iracema do Oeste","Irati","Iretama","Itaguajé","Itaipulândia","Itambaracá","Itambé","Itapejara d'Oeste","Itaperuçu","Itaúna do Sul","Ivaí","Ivaiporã","Ivaté","Ivatuba","Jaboti","Jacarezinho","Jaguapitã","Jaguariaíva","Jandaia do Sul","Janiópolis","Japira","Japurá","Jardim Alegre","Jardim Olinda","Jataizinho","Jesuítas","Joaquim Távora","Jundiaí do Sul","Juranda","Jussara","Kaloré","Lapa","Laranjal","Laranjeiras do Sul","Leópolis","Lidianópolis","Lindoeste","Loanda","Lobato","Londrina","Luiziana","Lunardelli","Lupionópolis","Mallet","Mamborê","Mandaguaçu","Mandaguari","Mandirituba","Manfrinópolis","Mangueirinha","Manoel Ribas","Marechal Cândido Rondon","Maria Helena","Marialva","Marilândia do Sul","Marilena","Mariluz","Maringá","Mariópolis","Maripá","Marmeleiro","Marquinho","Marumbi","Matelândia","Matinhos","Mato Rico","Mauá da Serra","Medianeira","Mercedes","Mirador","Miraselva","Missal","Moreira Sales","Morretes","Munhoz de Melo","Nossa Senhora das Graças","Nova Aliança do Ivaí","Nova América da Colina","Nova Aurora","Nova Cantu","Nova Esperança","Nova Esperança do Sudoeste","Nova Fátima","Nova Laranjeiras","Nova Londrina","Nova Olímpia","Nova Prata do Iguaçu","Nova Santa Bárbara","Nova Santa Rosa","Nova Tebas","Novo Itacolomi","Ortigueira","Ourizona","Ouro Verde do Oeste","Paiçandu","Palmas","Palmeira","Palmital","Palotina","Paraíso do Norte","Paranacity","Paranaguá","Paranapoema","Paranavaí","Pato Bragado","Pato Branco","Paula Freitas","Paulo Frontin","Peabiru","Perobal","Pérola","Pérola d'Oeste","Piên","Pinhais","Pinhal de São Bento","Pinhalão","Pinhão","Piraí do Sul","Piraquara","Pitanga","Pitangueiras","Planaltina do Paraná","Planalto","Ponta Grossa","Pontal do Paraná","Porecatu","Porto Amazonas","Porto Barreiro","Porto Rico","Porto Vitória","Prado Ferreira","Pranchita","Presidente Castelo Branco","Primeiro de Maio","Prudentópolis","Quarto Centenário","Quatiguá","Quatro Barras","Quatro Pontes","Quedas do Iguaçu","Querência do Norte","Quinta do Sol","Quitandinha","Ramilândia","Rancho Alegre","Rancho Alegre d'Oeste","Realeza","Rebouças","Renascença","Reserva","Reserva do Iguaçu","Ribeirão Claro","Ribeirão do Pinhal","Rio Azul","Rio Bom","Rio Bonito do Iguaçu","Rio Branco do Ivaí","Rio Branco do Sul","Rio Negro","Rolândia","Roncador","Rondon","Rosário do Ivaí","Sabáudia","Salgado Filho","Salto do Itararé","Salto do Lontra","Santa Amélia","Santa Cecília do Pavão","Santa Cruz de Monte Castelo","Santa Fé","Santa Helena","Santa Inês","Santa Isabel do Ivaí","Santa Izabel do Oeste","Santa Lúcia","Santa Maria do Oeste","Santa Mariana","Santa Mônica","Santa Tereza do Oeste","Santa Terezinha de Itaipu","Santana do Itararé","Santo Antônio da Platina","Santo Antônio do Caiuá","Santo Antônio do Paraíso","Santo Antônio do Sudoeste","Santo Inácio","São Carlos do Ivaí","São Jerônimo da Serra","São João","São João do Caiuá","São João do Ivaí","São João do Triunfo","São Jorge d'Oeste","São Jorge do Ivaí","São Jorge do Patrocínio","São José da Boa Vista","São José das Palmeiras","São José dos Pinhais","São Manoel do Paraná","São Mateus do Sul","São Miguel do Iguaçu","São Pedro do Iguaçu","São Pedro do Ivaí","São Pedro do Paraná","São Sebastião da Amoreira","São Tomé","Sapopema","Sarandi","Saudade do Iguaçu","Sengés","Serranópolis do Iguaçu","Sertaneja","Sertanópolis","Siqueira Campos","Sulina","Tamarana","Tamboara","Tapejara","Tapira","Teixeira Soares","Telêmaco Borba","Terra Boa","Terra Rica","Terra Roxa","Tibagi","Tijucas do Sul","Toledo","Tomazina","Três Barras do Paraná","Tunas do Paraná","Tuneiras do Oeste","Tupãssi","Turvo","Ubiratã","Umuarama","União da Vitória","Uniflor","Uraí","Ventania","Vera Cruz do Oeste","Verê","Virmond","Vitorino","Wenceslau Braz","Xambrê"
];

const escolasPorCidade = {
    "Curitiba": ["Colégio Estadual Paraná", "Colégio Estadual Xavier da Silva"],
    "Londrina": ["Colégio Estadual Vicente Rijo", "Colégio Estadual Marcelino Champagnat"],
    "Maringá": ["Colégio Estadual Gastão Vidigal", "Colégio Estadual Dr. Mário de Moraes"],
    "Cascavel": ["Colégio Estadual Eleodoro Ébanos Pereira", "Colégio Estadual Wilson Joffre"],
    "Matelândia": ["Colégio Estadual Euclides da Cunha", "Colégio Estadual Pedro Viriato Parigot de Souza"]
};

const disciplinas = [
    "Língua Portuguesa",
    "Matemática",
    "História",
    "Geografia",
    "Ciências / Biologia",
    "Física",
    "Química",
    "Língua Inglesa",
    "Educação Física",
    "Arte",
    "Filosofia",
    "Sociologia"
];

document.getElementById('estado').addEventListener('change', function() {
    const inputCidade = document.getElementById('cidade');
    const dataList = document.getElementById('lista_cidades');
    dataList.innerHTML = '';
    
    resetarCampo('escola', 'Selecione primeiro a Cidade');
    resetarCampo('disciplina', 'Selecione primeiro a Escola');

    if (this.value === "PR") {
        inputCidade.disabled = false;
        inputCidade.placeholder = "Digite ou selecione a cidade";

        cidadesPR.forEach(cidade => {
            const option = document.createElement('option');
            option.value = cidade;
            dataList.appendChild(option);
        });
    } else {
        inputCidade.disabled = true;
        inputCidade.value = '';
        inputCidade.placeholder = "Selecione primeiro o Estado";
    }
});

document.getElementById('cidade').addEventListener('change', function() {
    const selectEscola = document.getElementById('escola');
    const cidadeSelecionada = this.value;

    resetarCampo('disciplina', 'Selecione primeiro a Escola');

    if (cidadesPR.includes(cidadeSelecionada)) {
        selectEscola.disabled = false;
        selectEscola.innerHTML = '<option value="">Selecione a Escola</option>';

        const listaEscolas = escolasPorCidade[cidadeSelecionada] || [
            `Colégio Estadual de ${cidadeSelecionada}`,
            `Escola Estadual ${cidadeSelecionada}`
        ];

        listaEscolas.forEach(escola => {
            const option = document.createElement('option');
            option.value = escola;
            option.textContent = escola;
            selectEscola.appendChild(option);
        });
    } else {
        resetarCampo('escola', 'Selecione primeiro a Cidade');
    }
});

document.getElementById('escola').addEventListener('change', function() {
    const selectDisciplina = document.getElementById('disciplina');
    const escolaSelecionada = this.value;

    if (escolaSelecionada) {
        selectDisciplina.disabled = false;
        selectDisciplina.innerHTML = '<option value="">Selecione a Disciplina</option>';

        disciplinas.forEach(disc => {
            const option = document.createElement('option');
            option.value = disc;
            option.textContent = disc;
            selectDisciplina.appendChild(option);
        });
    } else {
        resetarCampo('disciplina', 'Selecione primeiro a Escola');
    }
});

function resetarCampo(idElemento, mensagem) {
    const elemento = document.getElementById(idElemento);
    elemento.disabled = true;
    elemento.innerHTML = `<option value="">${mensagem}</option>`;
}
</script>