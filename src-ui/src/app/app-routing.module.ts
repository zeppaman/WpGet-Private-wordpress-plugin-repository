import { Routes, RouterModule } from '@angular/router';
import { LoginComponent } from './login/login.component';
import { AuthGuard } from './_guard/auth.guard';
import { NgModule } from '@angular/core';
import { adminRouting} from './frame/frame-routing.module';


const appRoutes: Routes = [
    { path: 'login', component: LoginComponent },
    { path: 'logout', component: LoginComponent },
    { path: '**', component: LoginComponent }
  ];
  export const appRouting = RouterModule.forRoot(appRoutes);