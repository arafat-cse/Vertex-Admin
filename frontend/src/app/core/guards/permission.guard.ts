import { inject } from '@angular/core';
import { CanActivateFn, Router, ActivatedRouteSnapshot } from '@angular/router';
import { AuthService } from '../services/auth.service';
import { ToastService } from '../services/toast.service';

export const permissionGuard: CanActivateFn = (route: ActivatedRouteSnapshot) => {
  const auth = inject(AuthService);
  const router = inject(Router);
  const toast = inject(ToastService);

  const requiredPermission: string = route.data['permission'];

  if (!requiredPermission || auth.hasPermission(requiredPermission)) {
    return true;
  }

  toast.error('You do not have permission to access this page.');
  return router.createUrlTree(['/dashboard']);
};
