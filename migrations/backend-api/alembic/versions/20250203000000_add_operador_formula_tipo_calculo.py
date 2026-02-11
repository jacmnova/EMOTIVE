"""Add operador and formula to tipo_calculo.

Revision ID: 20250203000000
Revises: 20250128000000
Create Date: 2025-02-03

"""
from typing import Sequence, Union

from alembic import op
import sqlalchemy as sa

revision: str = "20250203000000"
down_revision: Union[str, None] = "20250128000000"
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


def upgrade() -> None:
    op.add_column("tipo_calculo", sa.Column("operador", sa.String(255), nullable=True))
    op.add_column("tipo_calculo", sa.Column("formula", sa.Text(), nullable=True))


def downgrade() -> None:
    op.drop_column("tipo_calculo", "formula")
    op.drop_column("tipo_calculo", "operador")
