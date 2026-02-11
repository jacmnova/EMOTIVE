"""Initial schema - all tables from SQLAlchemy models.

Revision ID: 20250128000000
Revises:
Create Date: 2025-01-28

"""
from typing import Sequence, Union

from alembic import op
import sqlalchemy as sa

revision: str = "20250128000000"
down_revision: Union[str, None] = None
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


def upgrade() -> None:
    # 1. tipo_calculo (sin FKs)
    op.create_table(
        "tipo_calculo",
        sa.Column("id", sa.Integer(), nullable=False),
        sa.Column("nome", sa.String(255), nullable=False),
        sa.Column("descricao", sa.String(500), nullable=True),
        sa.Column("created_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.Column("updated_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.PrimaryKeyConstraint("id"),
    )
    op.create_index(op.f("ix_tipo_calculo_id"), "tipo_calculo", ["id"], unique=False)

    # 2. users (cliente_id sin FK primero por dependencia circular con clientes)
    op.create_table(
        "users",
        sa.Column("id", sa.Integer(), nullable=False),
        sa.Column("name", sa.String(255), nullable=False),
        sa.Column("email", sa.String(255), nullable=False),
        sa.Column("password", sa.String(255), nullable=False),
        sa.Column("avatar", sa.String(255), nullable=True),
        sa.Column("email_verified_at", sa.DateTime(), nullable=True),
        sa.Column("verification_token", sa.String(64), nullable=True),
        sa.Column("password_reset_token", sa.String(64), nullable=True),
        sa.Column("password_reset_expires", sa.DateTime(), nullable=True),
        sa.Column("sa", sa.Boolean(), nullable=True, server_default="0"),
        sa.Column("admin", sa.Boolean(), nullable=True, server_default="0"),
        sa.Column("gestor", sa.Boolean(), nullable=True, server_default="0"),
        sa.Column("usuario", sa.Boolean(), nullable=True, server_default="0"),
        sa.Column("cliente_id", sa.Integer(), nullable=True),
        sa.Column("deleted_by", sa.Integer(), nullable=True),
        sa.Column("ativo", sa.Boolean(), nullable=True, server_default="1"),
        sa.Column("created_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.Column("updated_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.Column("deleted_at", sa.DateTime(), nullable=True),
        sa.PrimaryKeyConstraint("id"),
    )
    op.create_index(op.f("ix_users_email"), "users", ["email"], unique=True)
    op.create_index(op.f("ix_users_id"), "users", ["id"], unique=False)

    # 3. clientes (usuario_id -> users)
    op.create_table(
        "clientes",
        sa.Column("id", sa.Integer(), nullable=False),
        sa.Column("usuario_id", sa.Integer(), nullable=True),
        sa.Column("tipo", sa.Enum("cnpj", "cpf", "internacional", name="tipocliente"), nullable=False),
        sa.Column("cpf_cnpj", sa.String(255), nullable=False),
        sa.Column("nome_fantasia", sa.String(255), nullable=True),
        sa.Column("razao_social", sa.String(255), nullable=True),
        sa.Column("logo_url", sa.String(255), nullable=False, server_default="../vendor/adminlte/dist/img/client.png"),
        sa.Column("email", sa.String(255), nullable=True),
        sa.Column("contato", sa.String(255), nullable=True),
        sa.Column("telefone", sa.String(255), nullable=True),
        sa.Column("ativo", sa.Boolean(), nullable=True, server_default="1"),
        sa.Column("deleted_by", sa.Integer(), nullable=True),
        sa.Column("created_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.Column("updated_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.Column("deleted_at", sa.DateTime(), nullable=True),
        sa.ForeignKeyConstraint(["usuario_id"], ["users.id"], ondelete="SET NULL"),
        sa.PrimaryKeyConstraint("id"),
    )
    op.create_index(op.f("ix_clientes_cpf_cnpj"), "clientes", ["cpf_cnpj"], unique=True)
    op.create_index(op.f("ix_clientes_id"), "clientes", ["id"], unique=False)

    # 4. FK users.cliente_id -> clientes (batch mode for SQLite: no ALTER constraint)
    bind = op.get_bind()
    if bind.dialect.name == "sqlite":
        with op.batch_alter_table("users") as batch_op:
            batch_op.create_foreign_key("fk_users_cliente_id", "clientes", ["cliente_id"], ["id"], ondelete="SET NULL")
    else:
        op.create_foreign_key("fk_users_cliente_id", "users", "clientes", ["cliente_id"], ["id"], ondelete="SET NULL")

    # 5. formularios
    op.create_table(
        "formularios",
        sa.Column("id", sa.Integer(), nullable=False),
        sa.Column("nome", sa.String(255), nullable=False),
        sa.Column("label", sa.String(255), nullable=False),
        sa.Column("descricao", sa.Text(), nullable=False),
        sa.Column("instrucoes", sa.Text(), nullable=False),
        sa.Column("score_ini", sa.Integer(), nullable=False),
        sa.Column("score_fim", sa.Integer(), nullable=False),
        sa.Column("calculo_id", sa.Integer(), nullable=True),
        sa.Column("status", sa.Boolean(), nullable=True, server_default="0"),
        sa.Column("created_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.Column("updated_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.ForeignKeyConstraint(["calculo_id"], ["tipo_calculo.id"], ondelete="SET NULL"),
        sa.PrimaryKeyConstraint("id"),
    )
    op.create_index(op.f("ix_formularios_id"), "formularios", ["id"], unique=False)

    # 6. perguntas
    op.create_table(
        "perguntas",
        sa.Column("id", sa.Integer(), nullable=False),
        sa.Column("formulario_id", sa.Integer(), nullable=False),
        sa.Column("numero_da_pergunta", sa.Integer(), nullable=False),
        sa.Column("pergunta", sa.String(500), nullable=False),
        sa.Column("created_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.Column("updated_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.ForeignKeyConstraint(["formulario_id"], ["formularios.id"], ondelete="CASCADE"),
        sa.PrimaryKeyConstraint("id"),
    )
    op.create_index(op.f("ix_perguntas_id"), "perguntas", ["id"], unique=False)

    # 7. variaveis
    op.create_table(
        "variaveis",
        sa.Column("id", sa.Integer(), nullable=False),
        sa.Column("formulario_id", sa.Integer(), nullable=False),
        sa.Column("nome", sa.String(255), nullable=False),
        sa.Column("descricao", sa.Text(), nullable=True),
        sa.Column("tag", sa.String(10), nullable=False),
        sa.Column("B", sa.Integer(), nullable=False),
        sa.Column("M", sa.Integer(), nullable=False),
        sa.Column("A", sa.Integer(), nullable=True),
        sa.Column("baixa", sa.Text(), nullable=True),
        sa.Column("moderada", sa.Text(), nullable=True),
        sa.Column("alta", sa.Text(), nullable=True),
        sa.Column("r_baixa", sa.Text(), nullable=True),
        sa.Column("r_moderada", sa.Text(), nullable=True),
        sa.Column("r_alta", sa.Text(), nullable=True),
        sa.Column("d_baixa", sa.Text(), nullable=True),
        sa.Column("d_moderada", sa.Text(), nullable=True),
        sa.Column("d_alta", sa.Text(), nullable=True),
        sa.Column("created_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.Column("updated_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.ForeignKeyConstraint(["formulario_id"], ["formularios.id"], ondelete="CASCADE"),
        sa.PrimaryKeyConstraint("id"),
    )
    op.create_index(op.f("ix_variaveis_id"), "variaveis", ["id"], unique=False)

    # 8. pergunta_variavel
    op.create_table(
        "pergunta_variavel",
        sa.Column("id", sa.Integer(), nullable=False),
        sa.Column("pergunta_id", sa.Integer(), nullable=False),
        sa.Column("variavel_id", sa.Integer(), nullable=False),
        sa.Column("created_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.Column("updated_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.ForeignKeyConstraint(["pergunta_id"], ["perguntas.id"], ondelete="CASCADE"),
        sa.ForeignKeyConstraint(["variavel_id"], ["variaveis.id"], ondelete="CASCADE"),
        sa.PrimaryKeyConstraint("id"),
    )
    op.create_index(op.f("ix_pergunta_variavel_id"), "pergunta_variavel", ["id"], unique=False)

    # 9. respostas
    op.create_table(
        "respostas",
        sa.Column("id", sa.Integer(), nullable=False),
        sa.Column("user_id", sa.Integer(), nullable=False),
        sa.Column("pergunta_id", sa.Integer(), nullable=False),
        sa.Column("valor_resposta", sa.Integer(), nullable=False),
        sa.Column("created_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.Column("updated_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.ForeignKeyConstraint(["pergunta_id"], ["perguntas.id"], ondelete="CASCADE"),
        sa.ForeignKeyConstraint(["user_id"], ["users.id"], ondelete="CASCADE"),
        sa.PrimaryKeyConstraint("id"),
    )
    op.create_index(op.f("ix_respostas_id"), "respostas", ["id"], unique=False)

    # 10. midias (antes que usuario_formulario por midia_id)
    op.create_table(
        "midias",
        sa.Column("id", sa.Integer(), nullable=False),
        sa.Column("titulo", sa.String(255), nullable=False),
        sa.Column("tipo", sa.Enum("video", "url", name="tipomidia"), nullable=False),
        sa.Column("url", sa.String(500), nullable=True),
        sa.Column("arquivo", sa.String(500), nullable=True),
        sa.Column("formulario_id", sa.Integer(), nullable=False),
        sa.Column("created_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.Column("updated_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.ForeignKeyConstraint(["formulario_id"], ["formularios.id"], ondelete="CASCADE"),
        sa.PrimaryKeyConstraint("id"),
    )
    op.create_index(op.f("ix_midias_id"), "midias", ["id"], unique=False)

    # 11. cliente_formulario
    op.create_table(
        "cliente_formulario",
        sa.Column("id", sa.Integer(), nullable=False),
        sa.Column("cliente_id", sa.Integer(), nullable=True),
        sa.Column("formulario_id", sa.Integer(), nullable=True),
        sa.Column("quantidade", sa.Integer(), nullable=True),
        sa.Column("ativo", sa.Boolean(), nullable=True, server_default="1"),
        sa.Column("deleted_by", sa.Integer(), nullable=True),
        sa.Column("created_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.Column("updated_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.Column("deleted_at", sa.DateTime(), nullable=True),
        sa.ForeignKeyConstraint(["cliente_id"], ["clientes.id"], ondelete="SET NULL"),
        sa.ForeignKeyConstraint(["formulario_id"], ["formularios.id"], ondelete="SET NULL"),
        sa.PrimaryKeyConstraint("id"),
    )
    op.create_index(op.f("ix_cliente_formulario_id"), "cliente_formulario", ["id"], unique=False)

    # 12. usuario_formulario
    op.create_table(
        "usuario_formulario",
        sa.Column("id", sa.Integer(), nullable=False),
        sa.Column("usuario_id", sa.Integer(), nullable=True),
        sa.Column("formulario_id", sa.Integer(), nullable=True),
        sa.Column("status", sa.String(20), nullable=True, server_default="novo"),
        sa.Column("data_limite", sa.Date(), nullable=True),
        sa.Column("video_assistido", sa.Boolean(), nullable=True, server_default="0"),
        sa.Column("midia_id", sa.Integer(), nullable=True),
        sa.Column("deleted_by", sa.Integer(), nullable=True),
        sa.Column("created_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.Column("updated_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.Column("deleted_at", sa.DateTime(), nullable=True),
        sa.ForeignKeyConstraint(["formulario_id"], ["formularios.id"], ondelete="SET NULL"),
        sa.ForeignKeyConstraint(["midia_id"], ["midias.id"], ondelete="SET NULL"),
        sa.ForeignKeyConstraint(["usuario_id"], ["users.id"], ondelete="SET NULL"),
        sa.PrimaryKeyConstraint("id"),
    )
    op.create_index(op.f("ix_usuario_formulario_id"), "usuario_formulario", ["id"], unique=False)

    # 13. formulario_etapas
    op.create_table(
        "formulario_etapas",
        sa.Column("id", sa.Integer(), nullable=False),
        sa.Column("formulario_id", sa.Integer(), nullable=False),
        sa.Column("etapa", sa.Integer(), nullable=False),
        sa.Column("de", sa.Integer(), nullable=False),
        sa.Column("ate", sa.Integer(), nullable=False),
        sa.Column("created_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.Column("updated_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.ForeignKeyConstraint(["formulario_id"], ["formularios.id"], ondelete="CASCADE"),
        sa.PrimaryKeyConstraint("id"),
    )
    op.create_index(op.f("ix_formulario_etapas_id"), "formulario_etapas", ["id"], unique=False)

    # 14. analises
    op.create_table(
        "analises",
        sa.Column("id", sa.Integer(), nullable=False),
        sa.Column("user_id", sa.Integer(), nullable=False),
        sa.Column("formulario_id", sa.Integer(), nullable=False),
        sa.Column("texto", sa.Text(), nullable=False),
        sa.Column("created_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.Column("updated_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.ForeignKeyConstraint(["formulario_id"], ["formularios.id"], ondelete="CASCADE"),
        sa.ForeignKeyConstraint(["user_id"], ["users.id"], ondelete="CASCADE"),
        sa.PrimaryKeyConstraint("id"),
    )
    op.create_index(op.f("ix_analises_id"), "analises", ["id"], unique=False)


def downgrade() -> None:
    op.drop_index(op.f("ix_analises_id"), table_name="analises")
    op.drop_table("analises")
    op.drop_index(op.f("ix_formulario_etapas_id"), table_name="formulario_etapas")
    op.drop_table("formulario_etapas")
    op.drop_index(op.f("ix_usuario_formulario_id"), table_name="usuario_formulario")
    op.drop_table("usuario_formulario")
    op.drop_index(op.f("ix_cliente_formulario_id"), table_name="cliente_formulario")
    op.drop_table("cliente_formulario")
    op.drop_index(op.f("ix_midias_id"), table_name="midias")
    op.drop_table("midias")
    op.drop_index(op.f("ix_respostas_id"), table_name="respostas")
    op.drop_table("respostas")
    op.drop_index(op.f("ix_pergunta_variavel_id"), table_name="pergunta_variavel")
    op.drop_table("pergunta_variavel")
    op.drop_index(op.f("ix_variaveis_id"), table_name="variaveis")
    op.drop_table("variaveis")
    op.drop_index(op.f("ix_perguntas_id"), table_name="perguntas")
    op.drop_table("perguntas")
    op.drop_index(op.f("ix_formularios_id"), table_name="formularios")
    op.drop_table("formularios")
    bind = op.get_bind()
    if bind.dialect.name == "sqlite":
        with op.batch_alter_table("users") as batch_op:
            batch_op.drop_constraint("fk_users_cliente_id", type_="foreignkey")
    else:
        op.drop_constraint("fk_users_cliente_id", "users", type_="foreignkey")
    op.drop_index(op.f("ix_clientes_id"), table_name="clientes")
    op.drop_index(op.f("ix_clientes_cpf_cnpj"), table_name="clientes")
    op.drop_table("clientes")
    op.drop_index(op.f("ix_users_id"), table_name="users")
    op.drop_index(op.f("ix_users_email"), table_name="users")
    op.drop_table("users")
    op.drop_index(op.f("ix_tipo_calculo_id"), table_name="tipo_calculo")
    op.drop_table("tipo_calculo")
    op.execute("DROP TYPE IF EXISTS tipomidia")
    op.execute("DROP TYPE IF EXISTS tipocliente")
