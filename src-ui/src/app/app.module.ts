import { BrowserModule } from '@angular/platform-browser';
import { NgModule, APP_INITIALIZER } from '@angular/core';

import { AppComponent } from './app.component';

import {NoopAnimationsModule} from '@angular/platform-browser/animations';

import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { LoginComponent } from './login/login.component';
import { RouterModule, Routes } from '@angular/router';
import { FrameComponent } from './frame/frame.component';
import { NotFoundComponent } from './not-found/not-found.component';
import { HomeComponent } from './home/home.component';
import { UserComponent } from './user/user.component';
import { AuthenticationService } from './_service/authentication.services';
import { HttpModule } from '@angular/http';
import { FormsModule } from '@angular/forms';
import { adminRouting } from './frame/frame-routing.module';
import { appRouting } from './app-routing.module';
import { AuthGuard } from './_guard/auth.guard';

import {ToolbarModule} from 'primeng/primeng';
import {SidebarModule} from 'primeng/primeng';
import {SplitButtonModule} from 'primeng/primeng';
import {MenuModule} from 'primeng/primeng';
import { UserService } from './_service/user.service';
import { DataTableModule } from 'primeng/components/datatable/datatable';
import { SharedModule } from 'primeng/components/common/shared';
import { DialogModule } from 'primeng/components/dialog/dialog';
import { PasswordModule } from 'primeng/components/password/password';
import { MessagesModule } from 'primeng/components/messages/messages';
import { MessageModule } from 'primeng/components/message/message';
import { MessageService } from 'primeng/components/common/messageservice';
import { GrowlModule } from 'primeng/components/growl/growl';
import { BreadcrumbModule } from 'primeng/components/breadcrumb/breadcrumb';
import { RepositoryComponent } from './repository/repository.component';
import { PublishTokensComponent } from './publish-tokens/publish-tokens.component';
import { RepositoryService } from './_service/repository.service';
import { PublishTokenService } from './_service/publishtoken.service';
import {InputTextareaModule} from 'primeng/primeng';
import { DropdownModule } from 'primeng/components/dropdown/dropdown';
import { UploadPackageComponent } from './upload-package/upload-package.component';
import { PackagesComponent } from './packages/packages.component';
import {PanelModule} from 'primeng/primeng';
import {FileUploadModule} from 'primeng/primeng';
import { PackageService } from './_service/package.service';
import { ConfigurationService } from './_service/configuration.service';
import { APP_BASE_HREF } from '@angular/common';
import { HTTP_INTERCEPTORS } from '@angular/common/http';
import { AuthInterceptor } from './_service/http.interceptor';
import { HttpClientModule } from '@angular/common/http';
import { MenubarModule } from 'primeng/components/menubar/menubar';
import { PanelMenuModule } from 'primeng/components/panelmenu/panelmenu';
import {CardModule} from 'primeng/card';


export function configFactory(config:ConfigurationService) {
   return () => config.load();
}

export function baseHrefFactory (config: ConfigurationService)  {
  return window.location.href.substring(window.location.href.lastIndexOf(config.baseHref));
}



@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    FrameComponent,
    NotFoundComponent,
    HomeComponent,
    UserComponent,
    RepositoryComponent,
    PublishTokensComponent,
    UploadPackageComponent,
    PackagesComponent
  ],
  imports: [
    adminRouting,
    appRouting,
    BrowserModule,
    BrowserAnimationsModule,
    HttpModule,
    HttpClientModule,
    FormsModule,
    SplitButtonModule,
    MenuModule,
    SidebarModule,
    BrowserModule,
    ToolbarModule,
    BrowserAnimationsModule,
    DataTableModule,
    SharedModule,
    DialogModule,
    PasswordModule,
    MessagesModule,
    MessageModule,
    GrowlModule,
    BreadcrumbModule,
    InputTextareaModule,
    DropdownModule,
    PanelModule,
    FileUploadModule,
    MenubarModule,
    PanelMenuModule,
    CardModule

  ],
  providers: [AuthenticationService,
    AuthGuard, 
    UserService, 
    MessageService, 
    RepositoryService, 
    PublishTokenService,
    PackageService,
    ConfigurationService,
    {
      provide: HTTP_INTERCEPTORS,
      useClass: AuthInterceptor,
      multi: true
    },
    {
        provide: APP_INITIALIZER,
        useFactory:configFactory,
        deps: [ConfigurationService,HttpClientModule],
        multi: true
    },
    { provide: APP_BASE_HREF, 
      useFactory: baseHrefFactory,
      deps: [ConfigurationService,HttpClientModule],
     },

    
      
    
      
    ],
  bootstrap: [AppComponent]
})
export class AppModule { }
