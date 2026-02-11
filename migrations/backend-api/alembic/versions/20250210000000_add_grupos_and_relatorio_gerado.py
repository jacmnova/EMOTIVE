"""Add grupos and relatorio_gerado tables.

Revision ID: 20250210000000
Revises: 20250205000000
Create Date: 2026-02-10

"""
from typing import Sequence, Union

from alembic import op
import sqlalchemy as sa

revision: str = "20250210000000"
down_revision: Union[str, None] = "20250205000000"
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


def upgrade() -> None:
    op.create_table(
        "grupos",
        sa.Column("id", sa.Integer(), nullable=False),
        sa.Column("cliente_id", sa.Integer(), nullable=False),
        sa.Column("nome", sa.String(255), nullable=False),
        sa.Column("unidade", sa.String(255), nullable=True),
        sa.Column("area", sa.String(255), nullable=True),
        sa.Column("nivel_jerarquico", sa.String(255), nullable=True),
        sa.Column("tempo_empresa", sa.String(255), nullable=True),
        sa.Column("modelo_trabalho", sa.String(255), nullable=True),
        sa.Column("created_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.Column("updated_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.ForeignKeyConstraint(["cliente_id"], ["clientes.id"], ondelete="CASCADE"),
        sa.PrimaryKeyConstraint("id"),
    )
    op.create_index(op.f("ix_grupos_id"), "grupos", ["id"], unique=False)

    op.create_table(
        "relatorio_gerado",
        sa.Column("id", sa.Integer(), nullable=False),
        sa.Column("cliente_id", sa.Integer(), nullable=False),
        sa.Column("periodo_id", sa.Integer(), nullable=False),
        sa.Column("formulario_id", sa.Integer(), nullable=False),
        sa.Column("tipo", sa.String(50), nullable=True),
        sa.Column("user_id", sa.Integer(), nullable=False),
        sa.Column("created_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.ForeignKeyConstraint(["cliente_id"], ["clientes.id"], ondelete="CASCADE"),
        sa.ForeignKeyConstraint(["periodo_id"], ["periodos.id"], ondelete="CASCADE"),
        sa.ForeignKeyConstraint(["formulario_id"], ["formularios.id"], ondelete="CASCADE"),
        sa.ForeignKeyConstraint(["user_id"], ["users.id"], ondelete="CASCADE"),
        sa.PrimaryKeyConstraint("id"),
    )
    op.create_index(op.f("ix_relatorio_gerado_id"), "relatorio_gerado", ["id"], unique=False)


def downgrade() -> None:
    op.drop_index(op.f("ix_relatorio_gerado_id"), table_name="relatorio_gerado")
    op.drop_table("relatorio_gerado")
    op.drop_index(op.f("ix_grupos_id"), table_name="grupos")
    op.drop_table("grupos")
