"""Add projetos table and projeto_id to periodos.

Revision ID: 20250205000000
Revises: 20250204000001
Create Date: 2025-02-05

"""
from typing import Sequence, Union

from alembic import op
import sqlalchemy as sa

revision: str = "20250205000000"
down_revision: Union[str, None] = "20250204000001"
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


def upgrade() -> None:
    op.create_table(
        "projetos",
        sa.Column("id", sa.Integer(), nullable=False),
        sa.Column("cliente_id", sa.Integer(), nullable=False),
        sa.Column("nome", sa.String(255), nullable=False),
        sa.Column("descricao", sa.String(500), nullable=True),
        sa.Column("created_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.Column("updated_at", sa.DateTime(), server_default=sa.text("(CURRENT_TIMESTAMP)"), nullable=True),
        sa.ForeignKeyConstraint(["cliente_id"], ["clientes.id"], ondelete="CASCADE"),
        sa.PrimaryKeyConstraint("id"),
    )
    op.create_index(op.f("ix_projetos_id"), "projetos", ["id"], unique=False)

    op.add_column("periodos", sa.Column("projeto_id", sa.Integer(), nullable=True))
    conn = op.get_bind()
    if conn.dialect.name == "sqlite":
        with op.batch_alter_table("periodos") as batch_op:
            batch_op.create_foreign_key("fk_periodos_projeto_id", "projetos", ["projeto_id"], ["id"], ondelete="SET NULL")
    else:
        op.create_foreign_key("fk_periodos_projeto_id", "periodos", "projetos", ["projeto_id"], ["id"], ondelete="SET NULL")


def downgrade() -> None:
    conn = op.get_bind()
    if conn.dialect.name == "sqlite":
        with op.batch_alter_table("periodos") as batch_op:
            batch_op.drop_constraint("fk_periodos_projeto_id", type_="foreignkey")
    else:
        op.drop_constraint("fk_periodos_projeto_id", "periodos", type_="foreignkey")
    op.drop_column("periodos", "projeto_id")
    op.drop_index(op.f("ix_projetos_id"), table_name="projetos")
    op.drop_table("projetos")
