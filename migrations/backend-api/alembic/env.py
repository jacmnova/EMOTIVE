"""
Alembic environment: usa la configuración y modelos de la app.
La URL de la BD se toma de app.core.config (DATABASE_URL).
"""
import os
import sys
from logging.config import fileConfig

from sqlalchemy import engine_from_config
from sqlalchemy import pool
from alembic import context
from app.core.config import settings

# Añadir el directorio raíz del proyecto al path
sys.path.insert(0, os.path.realpath(os.path.join(os.path.dirname(__file__), "..")))

# Importar Base y todos los modelos para que estén registrados en metadata
from app.database import Base
from app.models import (
    User,
    Cliente,
    Formulario,
    Pergunta,
    Variavel,
    PerguntaVariavel,
    Resposta,
    ClienteFormulario,
    UsuarioFormulario,
    FormularioEtapa,
    Analise,
    Midia,
    TipoCalculo,
)

config = context.config
if config.config_file_name is not None:
    fileConfig(config.config_file_name)

# Metadata de los modelos de la app (todas las tablas)
target_metadata = Base.metadata

# Sobrescribir sqlalchemy.url con la de la app
config.set_main_option("sqlalchemy.url", settings.DATABASE_URL.replace("%", "%%"))


def run_migrations_offline() -> None:
    """Run migrations in 'offline' mode."""
    url = config.get_main_option("sqlalchemy.url")
    context.configure(
        url=url,
        target_metadata=target_metadata,
        literal_binds=True,
        dialect_opts={"paramstyle": "named"},
    )

    with context.begin_transaction():
        context.run_migrations()


def run_migrations_online() -> None:
    """Run migrations in 'online' mode."""
    connectable = engine_from_config(
        config.get_section(config.config_ini_section, {}),
        prefix="sqlalchemy.",
        poolclass=pool.NullPool,
    )

    with connectable.connect() as connection:
        context.configure(
            connection=connection,
            target_metadata=target_metadata,
        )

        with context.begin_transaction():
            context.run_migrations()


if context.is_offline_mode():
    run_migrations_offline()
else:
    run_migrations_online()
