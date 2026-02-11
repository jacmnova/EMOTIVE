"""
Endpoints de UsuarioFormulario (asignación de formularios a usuarios)
"""
from datetime import datetime, timezone
from typing import List, Optional
from fastapi import APIRouter, Depends, HTTPException, Query, status
from sqlalchemy.orm import Session
from app.database import get_db
from app.models import User, Formulario, UsuarioFormulario, ClienteFormulario, Grupo, UsuarioGrupo
from app.schemas.usuario_formulario import (
    UsuarioFormularioCreate,
    UsuarioFormularioUpdate,
    UsuarioFormularioResponse,
    UsuarioFormularioEmMassaCreate,
)
from app.core.permissions import require_admin, require_gestor
from app.core.security import create_questionario_access_token
from app.core.config import settings
from app.api.v1.auth import get_current_user_token
from app.services.email import send_invitacao_questionario, send_recordatorio_questionario

router = APIRouter()


@router.get("/", response_model=List[UsuarioFormularioResponse])
async def list_usuario_formulario(
    usuario_id: Optional[int] = Query(None, description="Filtrar por usuario"),
    periodo_id: Optional[int] = Query(None, description="Filtrar por período/onda"),
    current_user: User = Depends(require_gestor),
    db: Session = Depends(get_db),
):
    """
    Lista asignaciones formulario-usuario. Opcional: usuario_id, periodo_id.
    Admin/SA ven todo; gestor solo usuarios de su cliente.
    """
    query = db.query(UsuarioFormulario).filter(UsuarioFormulario.deleted_at == None)
    if usuario_id is not None:
        query = query.filter(UsuarioFormulario.usuario_id == usuario_id)
        if current_user.gestor and not current_user.admin and not current_user.sa:
            user = db.query(User).filter(User.id == usuario_id).first()
            if not user or user.cliente_id != current_user.cliente_id:
                raise HTTPException(status_code=403, detail="No tienes permiso para ver este usuario")
    elif current_user.gestor and not current_user.admin and not current_user.sa:
        # Sin usuario_id: gestor solo ve asignaciones de usuarios de su cliente
        query = query.join(User).filter(User.cliente_id == current_user.cliente_id)
    if periodo_id is not None:
        query = query.filter(UsuarioFormulario.periodo_id == periodo_id)
    rows = query.order_by(UsuarioFormulario.created_at.desc()).all()
    return [UsuarioFormularioResponse.model_validate(r) for r in rows]


@router.delete("/{usuario_formulario_id}")
async def delete_usuario_formulario(
    usuario_formulario_id: int,
    current_user: User = Depends(require_admin),
    db: Session = Depends(get_db),
):
    """
    Desasigna un formulario de un usuario (soft delete). Solo admin.
    """
    uf = db.query(UsuarioFormulario).filter(
        UsuarioFormulario.id == usuario_formulario_id,
        UsuarioFormulario.deleted_at == None,
    ).first()
    if not uf:
        raise HTTPException(status_code=404, detail="Asignación no encontrada")
    uf.deleted_at = datetime.now(timezone.utc)
    uf.deleted_by = current_user.id
    db.commit()
    return {"message": "Formulário desasignado do usuário"}


@router.post("/", response_model=UsuarioFormularioResponse)
async def asignar_formulario_usuario(
    data: UsuarioFormularioCreate,
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db)
):
    """
    Asigna un formulario a un usuario.
    Admin puede asignar cualquier formulario.
    Gestor solo puede asignar formularios de su cliente.
    """
    # Verificar que el usuario existe
    usuario = db.query(User).filter(User.id == data.usuario_id).first()
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")

    # Verificar que el formulario existe
    formulario = db.query(Formulario).filter(Formulario.id == data.formulario_id).first()
    if not formulario:
        raise HTTPException(status_code=404, detail="Formulario no encontrado")

    # Verificar permisos
    if current_user.gestor and not current_user.admin:
        # Gestor solo puede asignar a usuarios de su cliente
        if usuario.cliente_id != current_user.cliente_id:
            raise HTTPException(status_code=403, detail="No puedes asignar formularios a usuarios de otro cliente")
        
        # Verificar que el formulario está disponible para el cliente
        cliente_formulario = db.query(ClienteFormulario).filter(
            ClienteFormulario.cliente_id == current_user.cliente_id,
            ClienteFormulario.formulario_id == data.formulario_id,
            ClienteFormulario.ativo == True
        ).first()
        
        if not cliente_formulario:
            raise HTTPException(
                status_code=403,
                detail="Este formulario no está disponible para tu cliente"
            )
        
        # Verificar cantidad disponible
        if cliente_formulario.quantidade and cliente_formulario.quantidade <= 0:
            raise HTTPException(
                status_code=400,
                detail=f"No hay más formularios '{formulario.nome}' disponibles"
            )
        
        # Decrementar cantidad
        if cliente_formulario.quantidade:
            cliente_formulario.quantidade -= 1
            db.commit()

    # Verificar si ya existe la asignación
    existing = db.query(UsuarioFormulario).filter(
        UsuarioFormulario.usuario_id == data.usuario_id,
        UsuarioFormulario.formulario_id == data.formulario_id,
        UsuarioFormulario.deleted_at == None
    ).first()
    
    if existing:
        raise HTTPException(
            status_code=400,
            detail="Este formulário já foi incluído para este usuário"
        )

    # Crear asignación
    uf = UsuarioFormulario(
        usuario_id=data.usuario_id,
        formulario_id=data.formulario_id,
        periodo_id=data.periodo_id,
        status="novo",
        data_limite=data.data_limite,
        midia_id=data.midia_id,
        video_assistido=False
    )
    
    db.add(uf)
    db.commit()
    db.refresh(uf)

    if getattr(data, "enviar_invitacao", False) and usuario.email:
        form_nome = formulario.nome or formulario.label or f"Formulário #{formulario.id}"
        link = f"{settings.FRONTEND_URL}/responder?token={create_questionario_access_token(uf.id)}"
        send_invitacao_questionario(usuario.email, usuario.name or usuario.email, form_nome, link)

    return UsuarioFormularioResponse.model_validate(uf)

@router.post("/admin", response_model=UsuarioFormularioResponse)
async def asignar_formulario_admin(
    data: UsuarioFormularioCreate,
    current_user: User = Depends(require_admin),
    db: Session = Depends(get_db)
):
    """
    Asigna formulario a usuario (solo admin, sin validar cliente_formulario)
    """
    # Verificar que existe
    existing = db.query(UsuarioFormulario).filter(
        UsuarioFormulario.usuario_id == data.usuario_id,
        UsuarioFormulario.formulario_id == data.formulario_id,
        UsuarioFormulario.deleted_at == None
    ).first()
    
    if existing:
        raise HTTPException(
            status_code=400,
            detail="Este formulário já foi incluído para este usuário"
        )

    uf = UsuarioFormulario(
        usuario_id=data.usuario_id,
        formulario_id=data.formulario_id,
        periodo_id=data.periodo_id,
        status="novo",
        data_limite=data.data_limite,
        midia_id=data.midia_id,
        video_assistido=False
    )
    
    db.add(uf)
    db.commit()
    db.refresh(uf)

    if getattr(data, "enviar_invitacao", False):
        u = db.query(User).filter(User.id == data.usuario_id).first()
        f = db.query(Formulario).filter(Formulario.id == data.formulario_id).first()
        if u and u.email and f:
            form_nome = f.nome or f.label or f"Formulário #{f.id}"
            send_invitacao_questionario(u.email, u.name or u.email, form_nome)

    return UsuarioFormularioResponse.model_validate(uf)

@router.post("/{usuario_formulario_id}/finalizar")
async def finalizar_formulario(
    usuario_formulario_id: int,
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db)
):
    """
    Marca un formulario como completo
    """
    uf = db.query(UsuarioFormulario).filter(
        UsuarioFormulario.id == usuario_formulario_id
    ).first()
    
    if not uf:
        raise HTTPException(status_code=404, detail="Registro no encontrado")
    
    # Verificar permisos
    if uf.usuario_id != current_user.id:
        if not (current_user.admin or current_user.sa or current_user.gestor):
            raise HTTPException(status_code=403, detail="No tienes permiso")
    
    uf.status = "completo"
    db.commit()
    
    return {"message": "Formulário marcado como completo", "status": "completo"}

@router.post("/{usuario_formulario_id}/assistido")
async def marcar_assistido(
    usuario_formulario_id: int,
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db)
):
    """
    Marca el video como asistido
    """
    uf = db.query(UsuarioFormulario).filter(
        UsuarioFormulario.id == usuario_formulario_id
    ).first()
    
    if not uf:
        raise HTTPException(status_code=404, detail="Registro no encontrado")
    
    # Verificar permisos
    if uf.usuario_id != current_user.id:
        if not (current_user.admin or current_user.sa):
            raise HTTPException(status_code=403, detail="No tienes permiso")
    
    uf.video_assistido = True
    db.commit()
    
    return {"message": "Vídeo marcado como assistido", "video_assistido": True}

@router.put("/{usuario_formulario_id}", response_model=UsuarioFormularioResponse)
async def update_usuario_formulario(
    usuario_formulario_id: int,
    data: UsuarioFormularioUpdate,
    current_user: User = Depends(require_admin),
    db: Session = Depends(get_db)
):
    """
    Actualiza un registro de usuario_formulario (solo admin)
    """
    uf = db.query(UsuarioFormulario).filter(
        UsuarioFormulario.id == usuario_formulario_id
    ).first()

    if not uf:
        raise HTTPException(status_code=404, detail="Registro no encontrado")

    for field, value in data.model_dump(exclude_unset=True).items():
        setattr(uf, field, value)

    db.commit()
    db.refresh(uf)

    return UsuarioFormularioResponse.model_validate(uf)


@router.post("/{usuario_formulario_id}/enviar-recordatorio")
async def enviar_recordatorio(
    usuario_formulario_id: int,
    current_user: User = Depends(require_gestor),
    db: Session = Depends(get_db),
):
    """
    Envía email de recordatorio al usuario para completar el cuestionario.
    Solo para asignaciones pendentes. Gestor solo puede para usuarios de su cliente.
    """
    uf = db.query(UsuarioFormulario).filter(
        UsuarioFormulario.id == usuario_formulario_id,
        UsuarioFormulario.deleted_at == None,
    ).first()
    if not uf:
        raise HTTPException(status_code=404, detail="Atribuição não encontrada")
    if uf.status == "completo":
        raise HTTPException(status_code=400, detail="Este usuário já completou o questionário; não é necessário enviar lembrete.")
    user = db.query(User).filter(User.id == uf.usuario_id).first()
    if not user or not user.email:
        raise HTTPException(status_code=400, detail="Usuário sem e-mail cadastrado")
    if current_user.gestor and not current_user.admin and not current_user.sa:
        if user.cliente_id != current_user.cliente_id:
            raise HTTPException(status_code=403, detail="Sem permissão para enviar lembrete a este usuário")
    form = db.query(Formulario).filter(Formulario.id == uf.formulario_id).first()
    form_nome = form.nome or form.label if form else f"Formulário #{uf.formulario_id}"
    link = f"{settings.FRONTEND_URL}/responder?token={create_questionario_access_token(uf.id)}"
    ok = send_recordatorio_questionario(user.email, user.name or user.email, form_nome, link)
    if not ok:
        raise HTTPException(status_code=503, detail="Não foi possível enviar o e-mail; verifique a configuração de correio.")
    return {"message": "Lembrete enviado por e-mail com sucesso."}


@router.post("/em-massa")
async def asignar_em_massa(
    data: UsuarioFormularioEmMassaCreate,
    current_user: User = Depends(require_gestor),
    db: Session = Depends(get_db),
):
    """
    Atribui um formulário em massa a todos os usuários de um cliente que coincidem com os filtros (população).
    Gestor só pode usar seu cliente; admin/sa pode indicar cliente_id.
    Não cria duplicados: ignora usuários que já tenham essa atribuição (mesmo formulario_id + periodo_id).
    """
    if current_user.gestor and not current_user.admin and not current_user.sa:
        if current_user.cliente_id != data.cliente_id:
            raise HTTPException(status_code=403, detail="Só pode atribuir em massa ao seu cliente")
    form = db.query(Formulario).filter(Formulario.id == data.formulario_id).first()
    if not form:
        raise HTTPException(status_code=404, detail="Formulário não encontrado")

    unidade = data.unidade
    area = data.area
    nivel_jerarquico = data.nivel_jerarquico
    tempo_empresa = data.tempo_empresa
    modelo_trabalho = data.modelo_trabalho
    use_grupo_members = False
    grupo_user_ids = []
    if data.grupo_id:
        grupo = db.query(Grupo).filter(Grupo.id == data.grupo_id, Grupo.cliente_id == data.cliente_id).first()
        if not grupo:
            raise HTTPException(status_code=404, detail="Grupo não encontrado")
        # Grupos têm lista explícita de usuários (usuario_grupo)
        grupo_user_ids = [ug.usuario_id for ug in db.query(UsuarioGrupo).filter(UsuarioGrupo.grupo_id == data.grupo_id).all()]
        use_grupo_members = len(grupo_user_ids) > 0
        if not use_grupo_members:
            # Fallback: usar filtros do grupo como antes (unidade, area, etc.)
            unidade = grupo.unidade or unidade
            area = grupo.area or area
            nivel_jerarquico = grupo.nivel_jerarquico or nivel_jerarquico
            tempo_empresa = grupo.tempo_empresa or tempo_empresa
            modelo_trabalho = grupo.modelo_trabalho or modelo_trabalho

    if use_grupo_members:
        users = db.query(User).filter(
            User.id.in_(grupo_user_ids),
            User.cliente_id == data.cliente_id,
            User.deleted_at == None,
            User.ativo == True,
        ).all()
    else:
        query = db.query(User).filter(
            User.cliente_id == data.cliente_id,
            User.deleted_at == None,
            User.ativo == True,
        )
        if unidade and unidade.strip():
            query = query.filter(User.unidade == unidade.strip())
        if area and area.strip():
            query = query.filter(User.area == area.strip())
        if nivel_jerarquico and nivel_jerarquico.strip():
            query = query.filter(User.nivel_jerarquico == nivel_jerarquico.strip())
        if tempo_empresa and tempo_empresa.strip():
            query = query.filter(User.tempo_empresa == tempo_empresa.strip())
        if modelo_trabalho and modelo_trabalho.strip():
            query = query.filter(User.modelo_trabalho == modelo_trabalho.strip())
        users = query.all()
    criados = 0
    novos_para_email: list[tuple[User, Formulario]] = []
    for u in users:
        existing = db.query(UsuarioFormulario).filter(
            UsuarioFormulario.usuario_id == u.id,
            UsuarioFormulario.formulario_id == data.formulario_id,
            UsuarioFormulario.periodo_id == data.periodo_id,
            UsuarioFormulario.deleted_at == None,
        ).first()
        if not existing:
            uf = UsuarioFormulario(
                usuario_id=u.id,
                formulario_id=data.formulario_id,
                periodo_id=data.periodo_id,
                status="novo",
                data_limite=data.data_limite,
                midia_id=None,
                video_assistido=False,
            )
            db.add(uf)
            criados += 1
            if getattr(data, "enviar_invitacao", False) and u.email:
                novos_para_email.append((u, form))
    db.commit()
    for u, f in novos_para_email:
        uf = db.query(UsuarioFormulario).filter(
            UsuarioFormulario.usuario_id == u.id,
            UsuarioFormulario.formulario_id == f.id,
            UsuarioFormulario.periodo_id == data.periodo_id,
            UsuarioFormulario.deleted_at == None,
        ).first()
        if uf:
            link = f"{settings.FRONTEND_URL}/responder?token={create_questionario_access_token(uf.id)}"
            form_nome = f.nome or f.label or f"Formulário #{f.id}"
            send_invitacao_questionario(u.email, u.name or u.email, form_nome, link)
    return {"criados": criados, "total_populacao": len(users)}
