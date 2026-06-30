import { HttpInterceptorFn, HttpErrorResponse } from '@angular/common/http';
import { inject } from '@angular/core';
import { Router } from '@angular/router';
import { catchError, throwError } from 'rxjs';
import { StorageService } from '../services/storage.service';
import { ToastService } from '../services/toast.service';

export const errorInterceptor: HttpInterceptorFn = (req, next) => {
  const router = inject(Router);
  const storage = inject(StorageService);
  const toast = inject(ToastService);

  return next(req).pipe(
    catchError((err: HttpErrorResponse) => {
      switch (err.status) {
        case 401:
          storage.clearToken();
          router.navigate(['/auth/login']);
          toast.error('Your session has expired. Please log in again.');
          break;

        case 403:
          toast.error('Forbidden: You do not have access to this resource.');
          break;

        case 422: {
          const errors = err.error?.errors;
          if (errors && typeof errors === 'object') {
            const messages = Object.values(errors)
              .flat()
              .join(' ');
            toast.error(messages || 'Validation failed. Please check your input.');
          } else {
            toast.error(err.error?.message || 'Validation failed. Please check your input.');
          }
          break;
        }

        case 500:
          toast.error('Server error. Please try again later.');
          break;

        case 0:
          toast.error('Network error. Please check your internet connection.');
          break;

        default:
          if (err.error?.message) {
            toast.error(err.error.message);
          }
          break;
      }

      return throwError(() => err);
    })
  );
};
