"""Add periodos table and periodo_id to usuario_formulario.

Revision ID: 20250204000001
Revises: 20250204000000
Create Date: 2025-02-04

"""
from typing import Sequence, Union

from alembic import op
import sqlalchemy as sa

revision: str = "20250204000001"
down_revision: Union[str, None] = "20250204000000"
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


def upgrade() -> None:
    op.create_table(
        "periodos",
        sa.Column("id", sa.Integer(), nullable=False),
        sa.Column("cliente_id", sa.Integer(), nullable=False),
        sa.Column("nome", sa.String(255), nullable=False),
        sa.Column("descricao", sa.String(500), nullable=True),
        sa.Column("data_inicio", sa.Date(), nullable=True),
        sa.Column("data_fim", sa.Date(), nullable=True),
        sa.Column("created_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.Column("updated_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.ForeignKeyConstraint(["cliente_id"], ["clientes.id"], ondelete="CASCADE"),
        sa.PrimaryKeyConstraint("id"),
    )
    op.create_index(op.f("ix_periodos_id"), "periodos", ["id"], unique=False)

    op.add_column("usuario_formulario", sa.Column("periodo_id", sa.Integer(), nullable=True))
    # SQLite: add FK in batch
    conn = op.get_bind()
    if conn.dialect.name == "sqlite":
        with op.batch_alter_table("usuario_formulario") as batch_op:
            batch_op.create_foreign_key(
                "fk_usuario_formulario_periodo_id",
                "periodos",
                ["periodo_id"],
                ["id"],
                ondelete="SET NULL",
            )
    else:
        op.create_foreign_key(
            "fk_usuario_formulario_periodo_id",
            "usuario_formulario",
            "periodos",
            ["periodo_id"],
            ["id"],
            ondelete="SET NULL",
        )


def downgrade() -> None:
    conn = op.get_bind()
    if conn.dialect.name == "sqlite":
        with op.batch_alter_table("usuario_formulario") as batch_op:
            batch_op.drop_constraint("fk_usuario_formulario_periodo_id", type_="foreignkey")
    else:
        op.drop_constraint("fk_usuario_formulario_periodo_id", "usuario_formulario", type_="foreignkey")
    op.drop_column("usuario_formulario", "periodo_id")
    op.drop_index(op.f("ix_periodos_id"), table_name="periodos")
    op.drop_table("periodos")
