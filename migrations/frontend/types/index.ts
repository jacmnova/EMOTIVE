export interface User {
  id: number;
  name: string;
  email: string;
  avatar?: string | null;
  email_verified_at?: string | null;
  sa: boolean;
  admin: boolean;
  gestor: boolean;
  usuario: boolean;
  cliente_id?: number | null;
  ativo: boolean;
  /** Atributos para relatório corporativo / grupo */
  unidade?: string | null;
  area?: string | null;
  nivel_jerarquico?: string | null;
  tempo_empresa?: string | null;
  modelo_trabalho?: string | null;
  created_at: string;
  updated_at: string;
  /** Presente cuando el usuario actual está personificando a otro (id del admin/gestor). */
  impersonated_by?: number | null;
}

export interface TokenResponse {
  access_token: string;
  token_type: string;
  user: User;
}

export interface Formulario {
  id: number;
  nome: string;
  label: string;
  descricao: string;
  instrucoes: string;
  score_ini: number;
  score_fim: number;
  calculo_id?: number | null;
  status: boolean;
  created_at: string;
  updated_at: string;
  /** Número de perguntas (questões). Presente en listado. */
  num_perguntas?: number | null;
  /** Número de variáveis (dimensões). Presente en listado. */
  num_variaveis?: number | null;
}

export interface Etapa {
  id: number;
  formulario_id: number;
  etapa: number;
  de: number;
  ate: number;
  created_at?: string;
}

export interface TipoCalculo {
  id: number;
  nome: string;
  descricao: string | null;
  operador: string | null;
  formula: string | null;
  created_at: string;
  updated_at: string;
}

export interface Midia {
  id: number;
  titulo: string;
  tipo: string;
  formulario_id: number;
  url: string | null;
  arquivo: string | null;
  created_at: string;
  updated_at: string;
}

export type TipoCliente = "cpf" | "cnpj" | "internacional";

export interface Cliente {
  id: number;
  tipo: TipoCliente;
  cpf_cnpj: string;
  nome_fantasia?: string | null;
  razao_social?: string | null;
  email?: string | null;
  contato?: string | null;
  telefone?: string | null;
  usuario_id?: number | null;
  logo_url: string;
  ativo: boolean;
  created_at: string;
  updated_at: string;
}

export interface Pergunta {
  id: number;
  formulario_id: number;
  numero_da_pergunta: number;
  pergunta: string;
  created_at?: string;
  updated_at?: string;
}

export interface Variavel {
  id: number;
  formulario_id: number;
  nome: string;
  descricao?: string | null;
  tag: string;
  B: number;
  M: number;
  A?: number | null;
  baixa?: string | null;
  moderada?: string | null;
  alta?: string | null;
  r_baixa?: string | null;
  r_moderada?: string | null;
  r_alta?: string | null;
  d_baixa?: string | null;
  d_moderada?: string | null;
  d_alta?: string | null;
  created_at?: string;
  updated_at?: string;
}

export interface Periodo {
  id: number;
  cliente_id: number;
  projeto_id?: number | null;
  nome: string;
  descricao?: string | null;
  data_inicio?: string | null;
  data_fim?: string | null;
  created_at: string;
  updated_at: string;
}

export interface Projeto {
  id: number;
  cliente_id: number;
  nome: string;
  descricao?: string | null;
  created_at: string;
  updated_at: string;
}

export interface UsuarioFormulario {
  id: number;
  usuario_id: number;
  formulario_id: number;
  periodo_id?: number | null;
  status: string;
  data_limite: string | null;
  video_assistido: boolean;
  midia_id: number | null;
  created_at: string;
  updated_at: string;
}

export interface RespostaItem {
  pergunta_id: number;
  valor_resposta: number;
}

export interface Relatorio {
  pontuacoes?: { dimensao: string; pontuacao: number }[];
  indices?: { ee?: number; pr?: number; so?: number };
  ejes_analiticos?: Record<string, unknown>;
  iid?: number;
  nivel_risco?: string;
  plan?: string[];
  analise?: string;
}

export interface Grupo {
  id: number;
  cliente_id: number;
  nome: string;
  unidade?: string | null;
  area?: string | null;
  nivel_jerarquico?: string | null;
  tempo_empresa?: string | null;
  modelo_trabalho?: string | null;
  created_at?: string;
  updated_at?: string;
  /** Número de usuários no grupo (preenchido na listagem). */
  num_usuarios?: number | null;
}

export interface RelatorioGeradoItem {
  id: number;
  cliente_id: number;
  periodo_id: number;
  periodo_nome: string;
  formulario_id: number;
  formulario_nome: string;
  tipo: string | null;
  created_at: string | null;
}
