"""
Tests de carga básicos (usando pytest-benchmark opcional)
"""
import pytest
import time
from fastapi.testclient import TestClient


@pytest.mark.skip(reason="Requiere pytest-benchmark: pip install pytest-benchmark")
def test_multiple_login_requests(client, normal_user, benchmark):
    """Test de carga: múltiples requests de login"""
    def login_request():
        return client.post(
            "/api/v1/auth/login",
            data={"username": normal_user.email, "password": "password123"}
        )
    
    result = benchmark(login_request)
    assert result.status_code == 200


def test_concurrent_requests(client, admin_token):
    """Test básico de requests concurrentes"""
    import concurrent.futures
    
    def get_me():
        return client.get(
            "/api/v1/auth/me",
            headers={"Authorization": f"Bearer {admin_token}"}
        )
    
    start = time.time()
    with concurrent.futures.ThreadPoolExecutor(max_workers=10) as executor:
        futures = [executor.submit(get_me) for _ in range(10)]
        results = [f.result() for f in concurrent.futures.as_completed(futures)]
    
    elapsed = time.time() - start
    assert all(r.status_code == 200 for r in results)
    assert elapsed < 5.0  # Debe completar en menos de 5 segundos
