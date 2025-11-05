const express = require('express');
const mysql = require('mysql2');
const cors = require('cors');
const https = require('https')
var fs = require('fs')

const app = express();
const port = 4000;

app.use(cors());
app.use(express.json());

const connect = () => {
    const db = mysql.createConnection({
        // host: 'localhost',
        host: 'mysaas.com.br',
        user: 'limacabral',
        password: 'ro7n26',
        database: 'lcc_limacabral',
        // port: 3306
        port: 3309
    });

    db.connect((err) => {
        if (err) {
            console.error('error connecting: ' + err.stack);
            return;
        }
        console.log('connected as id ' + db.threadId);
    });

    return db;
};

app.get('/ini_juizos', (req, res) => {
    db = connect();
    const query = 'SELECT * FROM ini_juizos';
    db.query(query, (err, results) => {
        if (err) {
            res.status(500).send(err);
        } else {
            res.json(results);
        }
    });
});


app.get('/ini_acao', (req, res) => {
    db = connect();
    const query = 'SELECT * FROM ini_acao';
    db.query(query, (err, results) => {
        if (err) {
            res.status(500).send(err);
        } else {
            res.json(results);
        }
    });
});

app.get('/ini_estados', (req, res) => {
    db = connect();
    const query = 'SELECT * FROM ini_estados';
    db.query(query, (err, results) => {
        if (err) {
            res.status(500).send(err);
        } else {
            res.json(results);
        }
    });
});


app.get('/ini_grupo_trabalho', (req, res) => {
    db = connect();
    const query = 'SELECT * FROM ini_grupo_trabalho';
    db.query(query, (err, results) => {
        if (err) {
            res.status(500).send(err);
        } else {
            res.json(results);
        }
    });
});

app.get('/ini_materias', (req, res) => {
    db = connect();
    const query = 'SELECT * FROM ini_materias';
    db.query(query, (err, results) => {
        if (err) {
            res.status(500).send(err);
        } else {
            res.json(results);
        }
    });
});

app.get('/ini_usuarios', (req, res) => {
    db = connect();
    const query = 'SELECT * FROM ini_usuarios';
    db.query(query, (err, results) => {
        if (err) {
            res.status(500).send(err);
        } else {
            res.json(results);
        }
    });
});

app.get('/cad_credor', (req, res) => {
    db = connect();
    const query = 'SELECT * FROM cad_credor';
    db.query(query, (err, results) => {
        if (err) {
            res.status(500).send(err);
        } else {
            res.json(results);
        }
    });
});


app.post('/create_reu', (req, res) => {
    db = connect();
    let data = req.query;

    let query = `
    INSERT INTO ini_pessoas 
    SET nome = '${data.nome}', nome_fantasia = '${data.nome_fantasia}', tipo_pessoa='${data.tipo}', cpfcnpj = '${data.cpfcnpj}', sexo = '${data.sexo}',
        rua = '${data.rua}', cep = '${data.cep}', endereco_numero = '${data.numero}', complemento = '${data.complemento}', cidade = '${data.cidade}',
        email = '${data.email}', telefone = '${data.telefone}', estado_civil = '${data.estado_civil}', n_doc = '${data.numero_documento}',
        tipo_doc = '${data.tipo_documento}'
    `;

    db.query(query, (err, results) => {
        if (err) {
            res.status(500).send(err);
        } else {
            res.json(results);
        }
    });
})


app.post('/create_acao_cliente', (req, res) => {
    db = connect();

    let { id_reu, acao, credor, localizador, eletronico, materia, grupo_trabalho, responsavel,
        comarca, contrato, valor_causa, assunto, data_kit, url_file, url_arquivo_gerado, url_arquivo_editado, garantia, peticao, pa,
        agencia, responsavel_ada, avalistas } = req.query;

    let query = `
    INSERT INTO ini_acoes_cliente
    SET id_reu = '${id_reu}', acao = '${acao}', credor = '${credor}', materia = '${materia}', grupo_trabalho = '${grupo_trabalho}',
    adv_responsavel = '${responsavel}', responsavel_ada = '${responsavel_ada}', comarca = '${comarca}', eletronico='${eletronico}',
    localizador = '${localizador}', contrato = '${contrato}', valor_causa = '${valor_causa}', garantia = '${garantia}', assunto = '${assunto}',
    data_kit = '${data_kit}', tipo_peticao = '${peticao.tipo_peticao}', unidade='${peticao.unidade}', jurisprudencia = '${peticao.jurusprudencia}',
    arquivo_ajuizamento = '${url_file}', arquivo_peticao = '${url_arquivo_gerado}', arquivo_peticao_editado = '${url_arquivo_editado}', pa = '${pa}', agencia = '${agencia}';
    `

    query = query.replace(/undefined/g, '')


    db.query(query, (err, results) => {
        if (err) {
            res.status(500).send(err);
        } else {
            let id_acao_cliente = (results.insertId)

            let anexos = peticao.anexos;
            for (let i = 0; i < anexos.length; i++) {
                let anexo = anexos[i];
                let query_anexo = `
                INSERT INTO ini_anexos 
                SET numero = '${anexo.id}', descricao = '${anexo.label}', arquivo = '${anexo.nome}', id_acao = '${id_acao_cliente}'`.replace(/undefined/g, '')
                db.query(query_anexo)
            }


            let contratos = peticao.contratos;
            if (contratos) {
                for (let i = 0; i < contratos.length; i++) {
                    let contrato = contratos[i];

                    let query_contratos = `
                    INSERT INTO ini_contratos
                    SET id_acao = '${id_acao_cliente}', numero = '${contrato.numero_contrato}', data_contrato = '${contrato.data_contrato}',
                    qtd_parcelas = '${contrato.quantidade_parcelas}', valor_parcela = '${contrato.valor_parcela}', valor_contrato = '${contrato.valor_contrato}',
                    data_vencimento_parcela = '${contrato.data_vencimento_parcela}', antecipado = '${contrato.antecipado}', clausula = '${contrato.clausula}',
                    aceite = '${contrato.aceite}'
                    `.replace(/undefined/g, '')
                    db.query(query_contratos)
                }
            }

            if (avalistas) {
                for (let i = 0; i < avalistas.length; i++) {
                    let avalista = avalistas[i];

                    let query_aval = `
                    INSERT INTO ini_avalistas
                    SET tipo_pessoa = '${avalista.tipo_pessoa}', nome = '${avalista.nome}', cpfcnpj = '${avalista.cpfcnpj}',
                    rua = '${avalista.rua}', numero = '${avalista.numero}', bairro = '${avalista.bairro}',
                    complemento = '${avalista.complemento}', cep = '${avalista.cep}', cidade = '${avalista.cidade}',
                    email = '${avalista.email}', telefone = '${avalista.telefone}', id_acao = '${id_acao_cliente}',
                    documento = '${avalista.tipo_doc}', n_doc = '${avalista.n_doc}', profissao = '${avalista.profissao}',
                    estado_civil = '${avalista.estado_civil}', nome_fantasia = '${avalista.nome_fantasia}'
                    `.replace(/undefined/g, '')
                    db.query(query_aval)
                }

            }
            res.json(results);
        }
    });

})

app.get('/min_dados_processos', (req, res) => {
    db = connect();
    const data = req.query;
    const query = `select * from min_dados_processos where busca_processo = '${data.processo}'`;
    db.query(query, (err, results) => {
        if (err) {
            res.json(false)
        } {
            res.json(results)
        }
    })
})

app.get('/min_dados_termo_cob', (req, res) => {
    db = connect();
    const data = req.query;
    const query = `select * from min_dados_termo_cob where busca_cpfcnpj = '${data.processo}'`;
    db.query(query, (err, results) => {
        if (err) {
            res.json(false)
        } {
            res.json(results)
        }
    })
})

app.get('/min_avalistas', (req, res) => {
    db = connect();
    const data = req.query;
    const query = `select * from min_avalistas where busca_processo = '${data.processo}'`;
    db.query(query, (err, results) => {
        if (err) {
            res.json(false)
        } {
            res.json(results)
        }
    })
})

app.get('/min_garantias', (req, res) => {
    db = connect();
    const data = req.query;
    const query = `select * from min_garantias where busca_processo = '${data.processo}'`;
    db.query(query, (err, results) => {
        if (err) {
            res.json(false)
        } {
            res.json(results)
        }
    })
})

// ini_acoes_cliente - post
// ini_anexos - post
// ini_contratos - post
// ini_pesosas - post
// ini_peticao - post


// app.listen(port, () => {
//     console.log(`Server running on port ${port}`);
// });

https.createServer({
    key: fs.readFileSync('server.key'),
    cert: fs.readFileSync('server.cert')
  }, app)
  .listen(port, function () {
    console.log('Example app listening on port ' + port + '! Go to https://localhost:' + port + '/')
  })


module.exports = app;