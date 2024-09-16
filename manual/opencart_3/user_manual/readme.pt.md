

# Extensão de pagamentos ifthenpay Opencart 3

Download de versões da extensão ifthenpay para Opencart 3.
| | Opencart 3 [3.0.1.1 - 3.0.3.9] |
| ------------------------------------- | --------------------------------- |
| Link para descarregar instalador .ocmod.zip | [ifthenpay v1.3.0](https://github.com/ifthenpay/opencart/releases/download/1.3.0/ifthenpay.ocmod.zip) |

</br>
</br>

Ler em ![Portuguese](assets/pt.png) [Português](readme.pt.md), ou ![English](assets/en.png) [Inglês](readme.md)

[1. Introdução](#introdução)

[2. Compatibilidade](#compatibilidade)

[3. Instalação](#instalação)

[4. Configuração](#configuração)
 - [Chave Backoffice](#chave-backoffice)
 - [Multibanco](#multibanco)
 - [Multibanco com Referências Dinâmicas](#multibanco-com-referências-dinâmicas)
 - [MB WAY](#mb-way)
 - [Cartão de Crédito](#cartão-de-crédito)
 - [Payshop](#payshop)
 - [Cofidis Pay](#cofidis-pay)
 - [Ifthenpay Gateway](#ifthenpay-gateway)

[5. Outros](#outros)
 - [Requerer criação de conta adicional](#requerer-criação-de-conta-adicional)
 - [Limpeza de Configuração](#limpeza-de-configuração)
 - [Callback](#callback)
 - [Testar Callback](#testar-callback)
 - [Cronjob](#cronjob)
 - [Logs](#logs)
 - 

[6. Experiência do Utilizador Consumidor](#experiência-do-utilizador-consumidor)
 - [Pagar encomenda com Multibanco](#pagar-encomenda-com-multibanco)
 - [Pagar encomenda com Payshop](#pagar-encomenda-com-payshop)
 - [Pagar encomenda com MB WAY](#pagar-encomenda-com-mb-way)
 - [Pagar encomenda com Credit Card](#pagar-encomenda-com-cartão-de-crédito)
 - [Pagar encomenda com Cofidis Pay](#pagar-encomenda-com-cofidis-pay)
 - [Pagar encomenda com Ifthenpay Gateway](#pagar-encomenda-com-ifthenpay-gateway)


</br>

# Introdução
![img](assets/payment_methods_banner.png)
</br>
**Esta é a extensão ifthenpay para a plataforma ecommerce Opencart**

**Multibanco** é um método de pagamento que permite ao consumidor pagar com referência bancária.
Este extensão permite gerar referências de pagamento que o consumidor pode usar para pagar a sua encomenda numa caixa multibanco ou num serviço online de Home Banking. Este plugin usa a ifthenpay, uma das várias gateways disponíveis em Portugal.

**MB WAY** é a primeira solução inter-bancos que permite a compra e transferência imediata por via de smartphone e tablet.
Este extensão permite gerar um pedido de pagamento ao smartphone do consumidor, e este pode autorizar o pagamento da sua encomenda na aplicação MB WAY. Este plugin usa a ifthenpay, uma das várias gateways disponíveis em Portugal.

**Payshop** é um método de pagamento que permite ao consumidor pagar com referência Payshop.
Este extensão permite gerar uma referência de pagamento que o consumidor pode usar para pagar a sua encomenda num agente Payshop ou CTT. Este plugin usa a ifthenpay, uma das várias gateways disponíveis em Portugal.

**Cartão de Crédito** Esta extensão permite gerar um pagamento por Visa ou Master card, que o consumidor pode usar para pagar a sua encomenda. Este plugin usa a ifthenpay, uma das várias gateways disponíveis em Portugal.

**Cofidis Pay** é uma solução de pagamento que facilita o pagamento de compras ao dividir o valor até 12 prestações sem juros. Este plugin usa a ifthenpay, uma das várias gateways disponíveis em Portugal.

**Ifthenpay Gateway** é uma página de gateway que disponibiliza todos os métodos de pagamento acima mencionados. Este plugin usa a ifthenpay, uma das várias gateways disponíveis em Portugal.

**É necessário contrato com a ifthenpay**

Mais informações em [ifthenpay](https://ifthenpay.com). 

Adesão em [Adesão ifthenpay](https://www.ifthenpay.com/aderir/).

**Suporte**

Para suporte, por favor crie um ticked para suporte em [Suporte ifthenpay](https://helpdesk.ifthenpay.com/).

</br>


# Compatibilidade

Use a tabela abaixo para verificar a compatibilidade do extensão ifthenpay com a sua loja online.
| | Opencart 3 [3.0.1.1 - 3.0.3.9] | Opencart 4 |
| ------------------------- | ------------------------------ | ------------------------------ |
| ifthenpay v1.0.0 - v1.3.0 | Compatível | Não compatível |

</br>


# Instalação

Descarregue o ficheiro de instalação da extensão ifthenpay para Opencart 4 na página do github [ifthenpay v1.3.0](https://github.com/ifthenpay/opencart/releases/tag/v1.3.0).
Pode descarregar de varios pontos neste repositório:
 - o link nesta linha de instrução;
 - na página de releases, onde pode escolher a versão que precisa, mas note que a ifthenpay publica as releases para ambas versões do opencart 3 e 4 neste repositório;
 - no readme principal do repositório e escolhendo o download compativel com o opencart 3 (1); 
![img](assets/download_installer.png)
</br>

Aceda ao backoffice da sua loja online e escolha Extensões (1) -> Instalador (2), e clique em Upload (3).
![img](assets/upload_ocmodzip.png)
</br>

Selecione o ficheiro com extensão .ocmod.zip (1) que descarregou anteriormente, e clique em abrir (2).
![img](assets/select_ocmodzip.png)
</br>

Se o upload for bem-sucedido, será exibida uma mensagem de sucesso e o extensão será listado no grupo de extensões instaladas, mas apenas carregou a extensão, ainda necessita de instalar esta, par tal clique no botão de instalar (1).
![img](assets/ocmodzip_uploaded.png)
</br>

Se a instalação for bem sucedida, será exibida uma mensagem de sucesso e o botão de instalar mudará de cor para laranja.
![img](assets/ocmodzip_installed.png)
</br>


Após instalar a extensão, é necessário instalar os métodos de pagamento que pretende disponibilizar na sua loja online, para tal aceda à página de extensões de pagamentos.
Escolha Extensões (1) -> Extensões (2) -> Pagamentos (3).
![img](assets/extensions_payments.png)
</br>

Procure o método de pagamento que pretende instalar (exemplo Multibanco) e clique em "Instalar" (1).
![img](assets/install_method.png)

</br>


# Configuração

Após instalar o método de pagamento, é necessário configurar este com os dados da sua conta ifthenpay.
Clique em "Editar" (1) no método de pagamento que pretende configurar (exemplo Multibanco).
![img](assets/click_configure.png)

</br>


## Chave Backoffice

Cada configuração de método de pagamento requer a introdução da chave de backoffice para carregar as contas disponíveis.
A Chave Backoffice é dada na conclusão do contrato e é constituída por conjuntos de quatro algarismos separados por um traço (-).
Abaixo é apresentado o exemplo do Multibanco, esta ação é igual para os restantes métodos de pagamento.
Introduza a Chave de Backoffice (1) e clique em salvar (2), a página irá carregar novamente apresentando o formulário de configuração.
![img](assets/config_save_backofficekey.png)

</br>


## Multibanco

O método de pagamento Multibanco, gera referências por algoritmo e é usado se não desejar atribuir um tempo limite (em dias) para encomendas pagas com Multibanco.
A Entidade e Sub-Entidade são carregadas automaticamente, na introdução da Chave Backoffice.
Configure o método de pagamento, a imagem abaixo mostra um exemplo de configuração minimamente funcional.

1. **Estado** - Ativa o método de pagamento, exibindo-o no checkout da sua loja;
2. **Sandbox** - Previne a ativação do callback quando salva a configuração.
3. **Ativar Callback** - Ao ativar, o estado da encomenda será atualizado quando o pagamento for recebido;
4. **Habilitar Cron job de Cancelar** - Ao ativar, permite que o cronjob de cancelamento de encomendas execute neste método em específico (utilizado quando não deseja que o cronjob execute em todos os métodos de pagamento);
5. **Entidade** - Selecionar uma Entidade. Apenas pode selecionar uma das Entidades associadas à Chave Backoffice;
6. **Subentidade** - Selecionar uma Sub-Entidade. Apenas pode selecionar uma das Sub-Entidades associadas à Entidade escolhida anteriormente;
7. **Estado Cancelado** - Estado de encomenda usado quando a encomenda é cancelada;
8. **Estado Pendente** - Estado de encomenda usado na confirmação da encomenda;
9. **Estado Pago** - Estado de encomenda usado quando é recebido confirmação de pagamento;
10. **Zona Geo** - (opcional) Ao selecionar uma zona, apenas exibe este método de pagamento para encomendas com morada de envio pertencentes à zona selecionada;
11. **Valor Mínimo** - (opcional) Apenas exibe este método de pagamento para encomendas com valor superior ao valor inserido;
12. **Valor Máximo** - (opcional) Apenas exibe este método de pagamento para encomendas com valor inferior ao valor inserido;
13. **Exibir Logo do Método de Pagamento** - (opcional) Exibe o logo deste método de pagamento no checkout, desabilitar esta opção exibirá o Título do método de pagamento.
14. **Título** - Título que é exibido ao consumidor no checkout no caso de o logo não ser exibido;
15. **Ordenação** - (opcional) Ordena os métodos de pagamento na página de checkout de forma ascendente. Número mais baixo toma o primeiro lugar.

Clique em salvar (16) para guardar as alterações.
![img](assets/config_multibanco.png)

</br>


## Multibanco com Referências Dinâmicas
O método de pagamento Multibanco com Referências Dinâmicas, gera referências por pedido e é usado se desejar atribuir um tempo limite (em dias) para encomendas pagas com Multibanco.
A Entidade e Chave Multibanco são carregadas automaticamente, na introdução da Chave Backoffice.
Configure o método de pagamento, a imagem abaixo mostra um exemplo de configuração minimamente funcional.

Seguir os passos da configuração do Multibanco (indicados acima) com a seguinte alteração:

1. **Entidade** - Selecionar "Referências Dinâmicas de Multibanco", esta entidade só estará disponível para seleção se tiver efetuado contrato para criação de conta Multibanco com Referências Dinâmicas;
2. **Chave Multibanco** - Selecionar uma Chave Multibanco. Apenas pode selecionar uma das Chaves Multibanco associadas à Entidade escolhida anteriormente;
3. **Validade** - Selecionar o número de dias de validade da referência Multibanco. Ao deixar vazio, a referência Multibanco não expira.

Exemplos de validades:
 - escolhendo Validade de 0 dias, se uma encomenda for criada 22/03/2023 às 15:30, a referência Multibanco gerada expirará 22/03/2023 às 23:59, ou seja, no fim do dia em que foi gerada;
 - escolhendo Validade de 1 dia, se uma encomenda for criada 22/03/2023 às 9:30, a referência Multibanco gerada expirará 23/03/2023 às 23:59, ou seja, a referência Multibanco será válida durante o dia em que foi gerada mais 1 dia;
 - escolhendo Validade de 3 dias, se uma encomenda for criada 22/03/2023 às 20:30, a referência Multibanco gerada expirará 25/03/2023 às 23:59, ou seja, a referência Multibanco será válida durante o dia em que foi gerada mais 3 dias;

Clique em salvar (4) para guardar as alterações.
![img](assets/config_multibanco_dynamic.png)

</br>


## MB WAY
O método de pagamento MB WAY, usa um número de telemóvel dado pelo consumidor e gera um pedido de pagamento à aplicação MB WAY do smartphone deste, a qual pode aceitar ou recusar.
As Chaves MB WAY são carregadas automaticamente, na introdução da Chave Backoffice.
Configure o método de pagamento, a imagem abaixo mostra um exemplo de configuração minimamente funcional.

1. **Estado** - Ativa o método de pagamento, exibindo-o no checkout da sua loja;
2. **Sandbox** - Previne a ativação do callback quando salva a configuração.
3. **Ativar Callback** - Ao ativar, o estado da encomenda será atualizado quando o pagamento for recebido;
4. **Habilitar Cron job de Cancelar** - Ao ativar, permite que o cronjob de cancelamento de encomendas execute neste método em específico (utilizado quando não deseja que o cronjob execute em todos os métodos de pagamento);
5. **Chave MB WAY** - Selecionar uma Chave. Apenas pode selecionar uma das Chaves associadas à Chave Backoffice;
6.  **Estado Cancelado** - Estado de encomenda usado quando a encomenda é cancelada;
7.  **Estado Pendente** - Estado de encomenda usado na confirmação da encomenda;
8.  **Estado Pago** - Estado de encomenda usado quando é recebido confirmação de pagamento;
9.  **Zona Geo** - (opcional) Ao selecionar uma zona, apenas exibe este método de pagamento para encomendas com morada de envio pertencentes à zona selecionada;
10. **Valor Mínimo** - (opcional) Apenas exibe este método de pagamento para encomendas com valor superior ao valor inserido;
11. **Valor Máximo** - (opcional) Apenas exibe este método de pagamento para encomendas com valor inferior ao valor inserido;
12. **Exibir Logo do Método de Pagamento** - (opcional) Exibe o logo deste método de pagamento no checkout, desabilitar esta opção exibirá o Título do método de pagamento.
13. **Título** - Título que é exibido ao consumidor no checkout no caso de o logo não ser exibido;
14. **Ordenação** - (opcional) Ordena os métodos de pagamento na página de checkout de forma ascendente. Número mais baixo toma o primeiro lugar.

Clique em salvar (15) para guardar as alterações.
![img](assets/config_mbway.png)

</br>


## Cartão de Crédito
O método de pagamento Cartão de Crédito, permite pagar com cartão de crédito Visa ou Mastercard através da gateway ifthenpay.
As chaves de Cartão de Crédito são carregadas automaticamente, na introdução da Chave Backoffice.
Configure o método de pagamento, a imagem abaixo mostra um exemplo de configuração minimamente funcional.

1. **Estado** - Ativa o método de pagamento, exibindo-o no checkout da sua loja;
2. **Habilitar Cron job de Cancelar** - Ao ativar, permite que o cronjob de cancelamento de encomendas execute neste método em específico (utilizado quando não deseja que o cronjob execute em todos os métodos de pagamento);
3. **Chave Cartão de Crédito** - Selecionar uma Chave. Apenas pode selecionar uma das Chaves associadas à Chave Backoffice;
4. **Estado Cancelado** - Estado de encomenda usado quando a encomenda é cancelada;
5. **Estado Falhado** - Estado de encomenda usado quando o pedido de transação falha;
6. **Estado Pendente** - Estado de encomenda usado na confirmação da encomenda;
7. **Estado Pago** - Estado de encomenda usado quando é recebido confirmação de pagamento;
8.  **Zona Geo** - (opcional) Ao selecionar uma zona, apenas exibe este método de pagamento para encomendas com morada de envio pertencentes à zona selecionada;
9.  **Valor Mínimo** - (opcional) Apenas exibe este método de pagamento para encomendas com valor superior ao valor inserido;
10. **Valor Máximo** - (opcional) Apenas exibe este método de pagamento para encomendas com valor inferior ao valor inserido;
11. **Exibir Logo do Método de Pagamento** - (opcional) Exibe o logo deste método de pagamento no checkout, desabilitar esta opção exibirá o Título do método de pagamento.
12. **Título** - Título que é exibido ao consumidor no checkout no caso de o logo não ser exibido;
13. **Ordenação** - (opcional) Ordena os métodos de pagamento na página de checkout de forma ascendente. Número mais baixo toma o primeiro lugar.

Clique em salvar (14) para guardar as alterações.
![img](assets/config_ccard.png)

</br>


## Payshop
O método de pagamento Payshop, gera uma referência que pode ser paga em qualquer agente Payshop ou loja aderente.
As Chaves Payshop  são carregadas automaticamente, na introdução da Chave Backoffice.
Configure o método de pagamento, a imagem abaixo mostra um exemplo de configuração minimamente funcional.

1. **Estado** - Ativa o método de pagamento, exibindo-o no checkout da sua loja;
2. **Sandbox** - Previne a ativação do callback quando salva a configuração.
3. **Ativar Callback** - Ao ativar, o estado da encomenda será atualizado quando o pagamento for recebido;
4. **Habilitar Cron job de Cancelar** - Ao ativar, permite que o cronjob de cancelamento de encomendas execute neste método em específico (utilizado quando não deseja que o cronjob execute em todos os métodos de pagamento);
5. **Chave Payshop** - Selecionar uma Chave. Apenas pode selecionar uma das Chaves associadas à Chave Backoffice;
6. **Validade** - Selecionar o número de dias de validade da referência Payshop. De 1 a 99 dias, deixe vazio se não pretender que expire;
7.  **Estado Cancelado** - Estado de encomenda usado quando a encomenda é cancelada;
8. **Estado Pendente** - Estado de encomenda usado na confirmação da encomenda;
9. **Estado Pago** - Estado de encomenda usado quando é recebido confirmação de pagamento;
10. **Zona Geo** - (opcional) Ao selecionar uma zona, apenas exibe este método de pagamento para encomendas com morada de envio pertencentes à zona selecionada;
11. **Valor Mínimo** - (opcional) Apenas exibe este método de pagamento para encomendas com valor superior ao valor inserido;
12. **Valor Máximo** - (opcional) Apenas exibe este método de pagamento para encomendas com valor inferior ao valor inserido;
13. **Exibir Logo do Método de Pagamento** - (opcional) Exibe o logo deste método de pagamento no checkout, desabilitar esta opção exibirá o Título do método de pagamento.
14. **Título** - Título que é exibido ao consumidor no checkout no caso de o logo não ser exibido;
15. **Ordenação** - (opcional) Ordena os métodos de pagamento na página de checkout de forma ascendente. Número mais baixo toma o primeiro lugar.

Clique em salvar (16) para guardar as alterações.
![img](assets/config_payshop.png)

</br>


## Cofidis Pay

O método de pagamento Cofidis Pay permite ao consumidor pagar em prestações.
As Chaves Cofidis Pay  são carregadas automaticamente, na introdução da Chave Backoffice.
Configure o método de pagamento, a imagem abaixo mostra um exemplo de configuração minimamente funcional.

1. **Estado** - Ativa o método de pagamento, exibindo-o no checkout da sua loja;
2. **Sandbox** - Previne a ativação do callback quando salva a configuração.
3. **Ativar Callback** - Ao ativar, o estado da encomenda será atualizado quando o pagamento for recebido;
4. **Habilitar Cron job de Cancelar** - Ao ativar, permite que o cronjob de cancelamento de encomendas execute neste método em específico (utilizado quando não deseja que o cronjob execute em todos os métodos de pagamento);
5. **Chave Cofidis Pay** - Selecionar uma Chave. Apenas pode selecionar uma das Chaves associadas à Chave Backoffice;
6. **Estado Cancelado** - Estado de encomenda usado quando a encomenda é cancelada;
7. **Estado Aprovado** - Estado de encomenda usado quando aprovação é recebida;
8. **Estado Falhado** - Estado de encomenda usado quando o pedido de transação falha;
9. **Estado Pendente** - Estado de encomenda usado na confirmação da encomenda;
10. **Estado Pago** - Estado de encomenda usado quando é recebido confirmação de pagamento;
11. **Zona Geo** - (opcional) Ao selecionar uma zona, apenas exibe este método de pagamento para encomendas com morada de envio pertencentes à zona selecionada;
12. **Valor Mínimo** - (opcional) Apenas exibe este método de pagamento para encomendas com valor superior ao valor inserido;
13. **Valor Máximo** - (opcional) Apenas exibe este método de pagamento para encomendas com valor inferior ao valor inserido;
14. **Exibir Logo do Método de Pagamento** - (opcional) Exibe o logo deste método de pagamento no checkout, desabilitar esta opção exibirá o Título do método de pagamento.
15. **Título** - Título que é exibido ao consumidor no checkout no caso de o logo não ser exibido;
16. **Ordenação** - (opcional) Ordena os métodos de pagamento na página de checkout de forma ascendente. Número mais baixo toma o primeiro lugar.

Clique em salvar (17) para guardar as alterações.
![img](assets/config_cofidis.png)

</br>


## Ifthenpay Gateway

O método de pagamento Ifthenpay Gateway permite ao comsumidor, após ser redirecionado para página de gateway de pagamento, escolher um dos métodos de pagamento mencionados acima para pagar a sua encomenda.
As Chaves Ifthenpay Gateway são carregadas automaticamente, na introdução da Chave Backoffice.
Configure o método de pagamento, a imagem abaixo mostra um exemplo de configuração minimamente funcional.

1. **Estado** - Ativa o método de pagamento, exibindo-o no checkout da sua loja;
2. **Sandbox** - Previne a ativação do callback quando salva a configuração;
3. **Ativar Callback** - Ao ativar, o estado da encomenda será atualizado quando o pagamento for recebido;
4. **Habilitar Cron job de Cancelar** - Ao ativar, permite que o cronjob de cancelamento de encomendas execute neste método em específico (utilizado quando não deseja que o cronjob execute em todos os métodos de pagamento);
5. **Chave Ifthenpay Gateway** - Selecionar uma Chave. Apenas pode selecionar uma das Chaves associadas à Chave Backoffice;
6. **Métodos de Pagamento** - Selecionar uma conta por cada método de pagamento e colocar o visto na checkbox dos métodos que pretende exibir na página de gateway;
7. **Método de Pagamento por Defeito** - Selecionar um método de pagamento que estará selecionado por defeito na página da gateway quando o consumidor aceder a esta;
8.  **Validade** - Selecionar o número de dias de validade da referência Payshop. De 1 a 99 dias, deixe vazio se não pretender que expire;
9.  **Texto do Botão de Fechar Gateway** - Texto exíbido no botão de "Voltar para loja" na página da gateway;
10. **Estado Cancelado** - Estado de encomenda usado quando a encomenda é cancelada;
11. **Estado Pendente** - Estado de encomenda usado na confirmação da encomenda;
12. **Estado Pago** - Estado de encomenda usado quando é recebido confirmação de pagamento;
13. **Zona Geo** - (opcional) Ao selecionar uma zona, apenas exibe este método de pagamento para encomendas com morada de envio pertencentes à zona selecionada;
14. **Valor Mínimo** - (opcional) Apenas exibe este método de pagamento para encomendas com valor superior ao valor inserido;
15. **Valor Máximo** - (opcional) Apenas exibe este método de pagamento para encomendas com valor inferior ao valor inserido;
16. **Exibir Logo do Método de Pagamento** - (opcional) Exibe o logo deste método de pagamento no checkout, escolha uma de três opções:
    - habilitado - imagem por defeito: Exibe o logo ifthenpay gateway;
    - desabilitado: Exibe o Título do método de pagamento;
    - habilitado - imagem composta: Exibe uma imagem composta por todos os logos dos métodos de pagamento selecionados;  
17. **Title** - The title that appears to the consumer during checkout.
18. **Sort Order** - (optional) Sorts the payment methods on the checkout page in ascending order. Lower numbers take the first position.

Clique em salvar (19) para guardar as alterações.
![img](assets/config_ifthenpaygateway.png)

</br>



# Outros


## Requerer criação de conta adicional

Se já tem uma conta ifthenpay, mas não tem contratou um método de pagamento que agora precisa, pode fazer um pedido automático para a ifthenpay;
O tempo de resposta para este pedido é de 1 a 2 dias úteis, com a exceção do método de pagamento por Cartão de Crédito que pode exceder este tempo por ser necessário validação.
Para requerer a criação de uma conta adicional, aceda à página de configurações do método de pagamento que pretende contratar e clique em Requerer nova conta (1).
![img](assets/request_account.png)
</br>

No caso de já possuir uma conta Multibanco de referências estáticas e necessitar de uma conta para Multibanco com Referências Dinâmicas, poderá fazê-lo na página de configuração do Multibanco clicando no botão de Requerer conta Multibanco dinâmica (1) no fundo da página.
![img](assets/request_account_multibanco_dynamic.png)
</br>

Também pode requerer um método para Ifthenpay Gatway, seguindo o mesmo procedimento, clicando no botão (1).
![img](assets/request_gateway_payment_method.png)
</br>

Assim, a equipa da ifthenpay adicionará o método de pagamento à sua conta, atualizando a lista de métodos de pagamento disponíveis no seu extensão.

IMPORTANTE: Ao pedir uma conta para o método de pagamento por Cartão de Crédito, a equipa da ifthenpay irá contactá-lo para pedir mais informações sobre a sua loja online e o seu negócio antes de ativar o método de pagamento.

</br>


## Limpeza de Configuração

Esta funcionalidade permite limpar as configurações do método de pagamento e é útil nos seguintes cenários:
 - Se adquiriu uma nova Chave Backoffice e pretende atribuí-la ao seu site, mas já tem uma atualmente atribuída;
 - Se pediu a criação de uma conta adicional por telefone ou ticket e pretende atualizar a lista de métodos de pagamento para usar a nova conta.
 - Se pretende limpar as configurações do método de pagamento para voltar a configurar;

Na configuração do método de pagamento selecionado, clique no botão Limpar Configuração (1) e confirme a ação clicando em OK.

**Atenção, esta ação irá limpar as atuais configurações do método de pagamento**;

![img](assets/clear_configuration.png)
</br>

Após limpar a configuração, ser-lhe-á mais uma vez pedido para inserir a Chave Backoffice;
![img](assets/cleared_configuration.png)

</br>


## Callback

IMPORTANTE: apenas os métodos de pagamento Multibanco, MB WAY e Payshop permitem ativar o Callback. O cartão de crédito altera o estado da encomenda automaticamente sem utilizar o Callback.

O Callback é uma funcionalidade que quando ativa, permite que a sua loja receba a notificação de um pagamento bem-sucedido. Ao receber um pagamento com sucesso de uma encomenda, o servidor da ifthenpay comunica com a sua loja, mudando o estado da encomenda para "Em Processamento". Pode usar os pagamentos da ifthenpay sem ativar o Callback, mas as suas encomendas não atualizaram o estado automaticamente;

Como mencionado acima em configurações, para ativar o Callback, aceda à página de configurações do extensão e ative a opção Ativar Callback.
Após salvar as configurações, é executado o processo de associação da sua loja e método de pagamento aos servidores da ifthenpay, e será exibido um novo grupo "Callback" (apenas informativo) que apresenta estado do Callback (1), a chave anti-phishing (2), e a URL do Callback (3).

Após ativar o Callback não necessita de tomar mais nenhuma ação, o Callback está ativo e a funcionar.

![img](assets/callback.png)

</br>


## Testar Callback

Em cada página de configuração de um método de pagamento (exceto Cartão de Crédito), pode testar a funcionalidade do Callback clicando no botão "Testar Callback". Esta ação irá simular um pagamento bem-sucedido para uma encomenda na sua loja, alterando o estado da mesma. Necessita que o Callback esteja ativo para ter acesso a esta funcionalidade.

</br>

**Multibanco**: No backoffice, use os seguintes dados (1) e (2) dos detalhes de pagamento de encomenda
![img](assets/test_callback_data_multibanco.png)
</br>


e introduza-os nos respetivos campos (1) e (2) do formulário de teste de callback e clique em Testar (3).
Nota: a referência Multibanco deve ser inserida sem espaços e o valor, se tiver casas decimais, deve ser separado por um ponto.
![img](assets/test_callback_form_multibanco.png)

</br>


**MB WAY**: No backoffice, use os seguintes dados (1) e (2) dos detalhes de pagamento de encomenda
![img](assets/test_callback_data_payshop.png)
</br>


e introduza-os nos respetivos campos (1) e (2) do formulário de teste de callback e clique em Testar (3).
Nota: o valor, se tiver casas decimais, deve ser separado por um ponto.
![img](assets/test_callback_form_payshop.png)

</br>


**Payshop**: No backoffice, use os seguintes dados (1) e (2) dos detalhes de pagamento de encomenda
![img](assets/test_callback_data_payshop.png)
</br>


e introduza-os nos respetivos campos (1) e (2) do formulário de teste de callback e clique em Testar (3).
Nota: o valor, se tiver casas decimais, deve ser separado por um ponto.
![img](assets/test_callback_form_payshop.png)

</br>


**Cofidis Pay**: No backoffice, use os seguintes dados (1) e (2) dos detalhes de pagamento de encomenda
![img](assets/test_callback_data_cofidis.png)
</br>


e introduza-os nos respetivos campos (1) e (2) do formulário de teste de callback e clique em Testar (3).
Nota: o valor, se tiver casas decimais, deve ser separado por um ponto.
![img](assets/test_callback_form_cofidis.png)

</br>

**Ifthenpay Gateway**: No backoffice, use o ID de encomenda e o valor e introduza este nos respetivos campos (1) e (2) do formulário de teste de callback e clique em Testar (3)
Nota: o valor, se tiver casas decimais, deve ser separado por um ponto.
![img](assets/test_callback_form_ifthenpaygateway.png)

</br>


## Cronjob

Um cronjob é uma tarefa programada executada automaticamente em intervalos específicos no sistema. O extensão ifthenpay disponibiliza um cronjob para verificar o estado dos pagamentos, e cancelar encomendas que não foram pagas dentro do tempo limite configurado. A tabela abaixo mostra o tempo limite para cada método de pagamento, o qual o cronjob verifica e cancela as encomendas que não foram pagas dentro do tempo limite. Este tempo limite pode ser configurado apenas para o método de pagamento Multibanco com Referências Dinâmicas e Payshop.

| Método de Pagamento | Validade do pagamento       |
|---------------------|-----------------------------|
| Multibanco          | Não possui                  |
| Multibanco Dinâmico | Configurável de 1 a n dias  |
| MB WAY              | 30 minutos                  |
| Payshop             | Configurável de 1 a 99 dias |
| Cartão de Crédito   | 30 minutos                  |
| Ifthenpay Gateaway  | Configurável de 1 a 99 dias |


Para ativar o cronjob, aceda à página de configuração do extensão e ative a opção Habilitar Cron job de Cancelar e clique em salvar.
A página de configuração atualizará e apresentará a URL do cronjob (1), a qual deve ser adicionada ao seu servidor para que este execute o cronjob.
![img](assets/cronjob_url.png)

</br>


## Logs

Para facilitar a deteção de erros, a extensão ifthenpay regista os erros ocorridos durante a execução desta. Os logs são guardados num ficheiro de texto em storage/logs/, por exemplo storage/logs/multibanco.log. Para aceder aos logs, no root da pasta storage/ aceda à pasta logs/ e abra o ficheiro do método de pagamento que pretende analisar.

Nota: A localização da pasta storage é definida após a instalação do Opencart, e pode ser diferente para cada instalação.

</br>


# Experiência do Utilizador Consumidor

O seguinte descreve a experiência do utilizador consumidor ao usar os métodos de pagamento da ifthenpay numa instalação "stock" do Opencart 3, esta pode mudar com a adição de extensões de one-page-checkout.

Na página de checkout, após escolher o método de envio, o consumidor pode escolher o método de pagamento.

</br>


## Pagar encomenda com Multibanco

Selecionar o método de pagamento Multibanco (1) e clicar em Continuar (2).
![img](assets/select_multibanco.png)
</br>

Clicar em Confirmar Encomenda.

Será exibida a página de sucesso da encomenda, com a entidade, referência, validade e o valor a pagar.
Nota: no caso de attribuir uma conta Multibanco estática ou Multibanco com Referências Dinâmicas mas sem definir validade, não será exibido o tempo limite para pagamento.
![img](assets/payment_return_multibanco.png)

</br>


## Pagar encomenda com Payshop

Selecionar o método de pagamento Payshop (1) e clicar em Continuar (2).
![img](assets/select_payshop.png)
</br>

Clicar em Confirmar Encomenda.

Será exibida a página de sucesso da encomenda, com a referência, validade e o valor a pagar.
![img](assets/payment_return_payshop.png)

</br>


## Pagar encomenda com MB WAY

Selecionar o método de pagamento MB WAY (1) e clicar em Continuar (2).
![img](assets/select_mbway.png)
</br>

Preencher o número de telemóvel (1) e clicar em Confirmar Encomenda (2).
![img](assets/confirm_order_mbway.png)
</br>

No caso da configuração de Exibir Contagem estar ativa, será exibida a contagem decrescente do tempo limite para pagamento na página de sucesso da encomenda.
![img](assets/payment_return_mbway.png)
</br>

O contador atualizará automaticamente o estado do pagamento no caso de sucesso, rejeição por parte do utilizador da App MB WAY, expiração do tempo limite ou erro.
Em caso de sucesso será exibida a mensagem de sucesso.
</br>
![img](assets/payment_return_mbway_success.png)
</br>

Em caso de rejeição pelo utilizador será exibida a mensagem de rejeitado.
</br>
![img](assets/payment_return_mbway_rejected.png)
</br>

Em caso de expiração do tempo será exibida a mensagem de expirado.
</br>
![img](assets/payment_return_mbway_timeout.png)
</br>


Em caso de falha ao comunicar com a App MB WAY ou introdução de um número de telemóvel inválido, será exibida uma mensagem de erro.
</br>
![img](assets/payment_return_mbway_error.png)
</br>


Quando ocorre um erro ou atinge o tempo limite, ou recusa o pagamento na App MB WAY, o consumidor pode tentar novamente clicando em Reenviar notificação MB WAY.
</br>
![img](assets/payment_return_mbway_resend.png)
</br>


## Pagar encomenda com cartão de crédito

Selecionar o método de pagamento Cartão de Crédito (1) e clicar em Continuar (2).
![img](https://github.com/ifthenpay/opencart/raw/assets/version4/assets/select_ccard.png)
</br>

Clicar em Confirmar Encomenda.

Preencher os dados do cartão de crédito, número do cartão (1), data de validade (2), código de segurança (3), Nome no Cartão (4), e clicar em Pagar (5).
É possível voltar (6), regressando à página de checkout.
![img](https://github.com/ifthenpay/opencart/raw/assets/version4/assets/gateway_ccard.png)
</br>

Após o pagamento ser processado, será exibida a página de sucesso da encomenda.
![img](https://github.com/ifthenpay/opencart/raw/assets/version4/assets/payment_return_ccard.png)

</br>



## Pagar encomenda com Cofidis Pay

Selecionar o método de pagamento Cartão de Crédito (1) e clicar em Continuar (2).
![img](https://github.com/ifthenpay/opencart/raw/assets/version4/assets/select_cofidis.png)
</br>

Clicar em Confirmar Encomenda.

* Entre ou, se não tiver conta faça o registo com Cofidis Pay:
1. Clique "Avançar" para registar em Cofidis Pay;
2. Ou se tiver uma conta Cofidis Pay, preencha as suas credencias de acesso e clique entrar;
![img](https://github.com/ifthenpay/opencart/raw/assets/version4/assets/cofidis_payment_1.png)
</br>

* Número de prestações, faturação e dados pessoais:
1. Selecione o número de prestações que deseja;
2. Verifique o sumário do plano de pagamento;
3. Preencha os seus dados pessoais e de faturação;
4. Clique em "Avançar" para continuar;
![img](https://github.com/ifthenpay/opencart/raw/assets/version4/assets/cofidis_payment_2.png)
</br>

* Termos e condições:
1. Selecione "Li e autorizo" para concordar com os termos e condições;
2. Clique em "Avançar"
![img](https://github.com/ifthenpay/opencart/raw/assets/version4/assets/cofidis_payment_3.png)
</br>

* Formalização do acordo:
1. Clique em "Enviar código";
![img](https://github.com/ifthenpay/opencart/raw/assets/version4/assets/cofidis_payment_4.png)
</br>

* Código de autenticação da formalização do acordo:
1. Preencha o com o código que recebeu no telemóvel;
1. Clique em "Confirmar código";
![img](https://github.com/ifthenpay/opencart/raw/assets/version4/assets/cofidis_payment_5.png)
</br>

* Resumo e Pagamento:
1. Preencha com os detalhes do seu cartão de crédito(número, data de expiração e CW), e clique em "Validar";
![img](https://github.com/ifthenpay/opencart/raw/assets/version4/assets/cofidis_payment_6.png)
</br>

* Sucesso e voltar à loja:
1. Clique no icone para voltar à loja;
![img](https://github.com/ifthenpay/opencart/raw/assets/version4/assets/cofidis_payment_7.png)
</br>

Após o pagamento ser processado, será exibida a página de sucesso da encomenda.
![img](https://github.com/ifthenpay/opencart/raw/assets/version4/assets/payment_return_cofidis.png)
</br>



## Pagar encomenda com Ifthenpay Gateway

Selecionar o método de pagamento Ifthenpay Gateway (1) e clicar em Continuar (2).
![img](assets/select_ifthenpaygateway.png)
</br>

Clicar em Confirmar Encomenda.

Selecionar um dos métodos de pagamento disponíveis na página da gateway (1).
![img](assets/ifthenpaygateway_payment_1.png)
</br>

No caso do método de pagamento Multibanco, a entidade, referência e valor serão exibidos.
Aqui o consumidor pode fazer uma das duas ações:
 - em caso de método de pagamento offline, guardar os dados de pagamento, clicar em fechar a gateway no botão (2) e pagar mais tarde;
 - pagar no momento e clicar no botão de confirmar pagamento (3) para verificar o pagamento; 
![img](assets/ifthenpaygateway_payment_2.png)
</br>

Se o consumidor não pagou no momento e não guardou os dados de pagamento, ainda pode aceder mais tarde a estes através do link de acesso à gateway encontrado no histórico de encomenda na conta de utilizador ou email de confirmação de encomenda. 
![img](assets/ifthenpaygateway_payment_3.png)
</br>


You have reached the end of the ifthenpay extension manual for Opencart 3.
