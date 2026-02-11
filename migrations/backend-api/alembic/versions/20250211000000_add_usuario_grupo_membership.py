"""Add usuario_grupo table (users belonging to a group).

Revision ID: 20250211000000
Revises: 20250210000000
Create Date: 2026-02-11

"""
from typing import Sequence, Union

from alembic import op
import sqlalchemy as sa

revision: str = "20250211000000"
down_revision: Union[str, None] = "20250210000000"
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


def upgrade() -> None:
    op.create_table(
        "usuario_grupo",
        sa.Column("id", sa.Integer(), nullable=False),
        sa.Column("usuario_id", sa.Integer(), nullable=False),
        sa.Column("grupo_id", sa.Integer(), nullable=False),
        sa.ForeignKeyConstraint(["usuario_id"], ["users.id"], ondelete="CASCADE"),
        sa.ForeignKeyConstraint(["grupo_id"], ["grupos.id"], ondelete="CASCADE"),
        sa.PrimaryKeyConstraint("id"),
        sa.UniqueConstraint("usuario_id", "grupo_id", name="uq_usuario_grupo"),
    )
    op.create_index(op.f("ix_usuario_grupo_id"), "usuario_grupo", ["id"], unique=False)
    op.create_index("ix_usuario_grupo_grupo_id", "usuario_grupo", ["grupo_id"], unique=False)
    op.create_index("ix_usuario_grupo_usuario_id", "usuario_grupo", ["usuario_id"], unique=False)


def downgrade() -> None:
    op.drop_index("ix_usuario_grupo_usuario_id", table_name="usuario_grupo")
    op.drop_index("ix_usuario_grupo_grupo_id", table_name="usuario_grupo")
    op.drop_index(op.f("ix_usuario_grupo_id"), table_name="usuario_grupo")
    op.drop_table("usuario_grupo")
