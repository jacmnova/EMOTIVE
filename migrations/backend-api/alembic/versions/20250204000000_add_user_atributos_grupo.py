"""Add user attributes for corporate report (unidade, area, nivel, etc).

Revision ID: 20250204000000
Revises: 20250203000000
Create Date: 2025-02-04

"""
from typing import Sequence, Union

from alembic import op
import sqlalchemy as sa

revision: str = "20250204000000"
down_revision: Union[str, None] = "20250203000000"
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


def upgrade() -> None:
    op.add_column("users", sa.Column("unidade", sa.String(255), nullable=True))
    op.add_column("users", sa.Column("area", sa.String(255), nullable=True))
    op.add_column("users", sa.Column("nivel_jerarquico", sa.String(255), nullable=True))
    op.add_column("users", sa.Column("tempo_empresa", sa.String(255), nullable=True))
    op.add_column("users", sa.Column("modelo_trabalho", sa.String(255), nullable=True))


def downgrade() -> None:
    op.drop_column("users", "modelo_trabalho")
    op.drop_column("users", "tempo_empresa")
    op.drop_column("users", "nivel_jerarquico")
    op.drop_column("users", "area")
    op.drop_column("users", "unidade")
